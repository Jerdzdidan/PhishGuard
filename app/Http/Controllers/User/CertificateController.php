<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\CertificateMail;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateController extends Controller
{
    /**
     * Check if user is eligible for certificate and issue if needed
    */
    public function checkEligibility()
    {
        $user = Auth::user();

        if ($user->certificate) {
            return response()->json([
                'eligible' => false,
                'has_certificate' => true,
                'certificate' => $user->certificate,
                'post_assessment_ready' => false,
            ]);
        }

        if ($section = $user->getCertificateEligibleSection()) {
            $certificate = $user->issueCertificate($section->id);
            $emailResult = $certificate
                ? $this->sendCertificateToUser($user, $certificate)
                : ['success' => false, 'message' => 'Certificate could not be created.'];

            return response()->json([
                'eligible' => true,
                'just_earned_certificate' => true,
                'has_certificate' => false,
                'certificate' => $certificate,
                'email_sent' => $emailResult['success'],
                'email_message' => $emailResult['message'],
                'message' => 'Congratulations! You have earned your certificate!',
            ]);
        }

        if ($section = $user->getPendingPostAssessmentSection()) {
            return response()->json([
                'eligible' => false,
                'has_certificate' => false,
                'post_assessment_ready' => true,
                'section' => [
                    'id' => $section->id,
                    'name' => $section->name,
                    'teacher' => $section->teacher
                        ? trim($section->teacher->first_name . ' ' . $section->teacher->last_name)
                        : null,
                ],
                'post_assessment_url' => route('assessment.post', \Illuminate\Support\Facades\Crypt::encryptString($section->id)),
            ]);
        }

        return response()->json([
            'eligible' => false,
            'has_certificate' => false,
            'certificate' => null,
            'post_assessment_ready' => false,
        ]);
    }

    /**
     * View certificate
     */
    public function view()
    {
        $user = Auth::user();
        $certificate = $this->ensureCertificate($user);

        if (!$certificate) {
            return redirect()->route('user.home')
                ->with('error', 'You have not earned a certificate yet.');
        }

        return view('user.certificate.view', compact('certificate', 'user'));
    }

    /**
     * Download certificate as PDF
     */
    public function download()
    {
        $user = Auth::user();
        $certificate = $this->ensureCertificate($user);

        if (!$certificate) {
            return redirect()->route('user.home')
                ->with('error', 'You have not earned a certificate yet.');
        }

        // Return the certificate view that can be printed/saved as PDF
        return view('user.certificate.download', compact('certificate', 'user'));
    }

    /**
     * Generate and download certificate PDF
     */
    public function generate()
    {
        $user = Auth::user();
        $certificate = $this->ensureCertificate($user);

        if (!$certificate) {
            return redirect()->route('user.home')
                ->with('error', 'You have not earned a certificate yet.');
        }

        try {
            $outputPath = $this->generateCertificatePdf($user, $certificate);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Download the PDF
        return response()->download($outputPath, $certificate->certificate_number . '.pdf', [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Send certificate to user's email
     */
    public function sendEmail()
    {
        $user = Auth::user();
        $certificate = $this->ensureCertificate($user);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'You have not earned a certificate yet.'
            ], 400);
        }

        $emailResult = $this->sendCertificateToUser($user, $certificate);

        if (!$emailResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $emailResult['message']
            ], 500);
        }

        return response()->json($emailResult);
    }

    private function ensureCertificate(User $user): ?Certificate
    {
        if ($user->certificate) {
            return $user->certificate;
        }

        $section = $user->getCertificateEligibleSection();
        if (!$section) {
            return null;
        }

        $certificate = $user->issueCertificate($section->id);
        if ($certificate) {
            session()->flash('certificate_earned', true);
            $this->sendCertificateToUser($user, $certificate);
        }

        return $certificate;
    }

    private function sendCertificateToUser(User $user, Certificate $certificate): array
    {
        try {
            $outputPath = $this->generateCertificatePdf($user, $certificate, '_email');
            Mail::to($user->email)->send(new CertificateMail($user, $certificate, $outputPath));

            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            return [
                'success' => true,
                'message' => 'Certificate has been sent to ' . $user->email,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to send certificate email', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'certificate_id' => $certificate->id,
            ]);

            return [
                'success' => false,
                'message' => 'Certificate was created, but the email could not be sent automatically.',
            ];
        }
    }

    private function generateCertificatePdf(User $user, Certificate $certificate, string $suffix = ''): string
    {
        $certNumber = $certificate->certificate_number;
        $outputPath = storage_path('app/certificates/' . $certNumber . $suffix . '.pdf');
        $templatePath = public_path('img/certificate-template.png');
        $scriptPath = base_path('generate_certificate.py');
        $pythonBinary = $this->resolvePythonBinary();

        if (!file_exists(storage_path('app/certificates'))) {
            mkdir(storage_path('app/certificates'), 0755, true);
        }

        if (!file_exists($templatePath)) {
            $uploadedTemplate = public_path('img/1.png');
            if (file_exists($uploadedTemplate)) {
                $templatePath = $uploadedTemplate;
            } else {
                throw new \RuntimeException('Certificate template not found. Please contact administrator.');
            }
        }

        $this->ensurePythonDependencies($pythonBinary);

        $command = sprintf(
            '%s %s %s %s %s %s %s %s %s %s',
            escapeshellarg($pythonBinary),
            escapeshellarg($scriptPath),
            escapeshellarg($outputPath),
            escapeshellarg(trim($user->first_name . ' ' . $user->last_name)),
            escapeshellarg($certNumber),
            escapeshellarg($certificate->issued_at->format('F d, Y')),
            escapeshellarg($certificate->total_lessons_completed),
            escapeshellarg(number_format($certificate->average_quiz_score ?? 0, 2)),
            escapeshellarg(number_format($certificate->average_simulation_score ?? 0, 2)),
            escapeshellarg($templatePath)
        );

        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($outputPath)) {
            Log::error('Certificate generation failed', [
                'command' => $command,
                'output' => $output,
                'return_var' => $returnVar,
                'certificate_id' => $certificate->id,
            ]);

            throw new \RuntimeException('Failed to generate certificate PDF.');
        }

        return $outputPath;
    }

    private function resolvePythonBinary(): string
    {
        $candidates = array_filter([
            env('CERTIFICATE_PYTHON_BIN'),
            PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3',
            PHP_OS_FAMILY === 'Windows' ? 'python3' : 'python',
        ]);

        foreach ($candidates as $candidate) {
            exec(sprintf('%s --version 2>&1', escapeshellarg($candidate)), $output, $returnVar);

            if ($returnVar === 0) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Python runtime not found. Please install Python or set CERTIFICATE_PYTHON_BIN in your .env file.');
    }

    private function ensurePythonDependencies(string $pythonBinary): void
    {
        exec(
            sprintf(
                '%s -c %s 2>&1',
                escapeshellarg($pythonBinary),
                escapeshellarg('import reportlab; from PIL import Image')
            ),
            $output,
            $returnVar
        );

        if ($returnVar === 0) {
            return;
        }

        Log::error('Certificate generator dependencies are missing', [
            'python_binary' => $pythonBinary,
            'output' => $output,
        ]);

        throw new \RuntimeException(
            'Certificate generator dependencies are missing. Install reportlab and Pillow for the configured Python runtime.'
        );
    }
}
