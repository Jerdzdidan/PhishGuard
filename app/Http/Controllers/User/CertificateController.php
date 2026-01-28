<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    /**
     * Check if user is eligible for certificate and issue if needed
    */
    public function checkEligibility()
    {
        $user = Auth::user();
        
        if ($user->isEligibleForCertificate()) {
            $certificate = $user->issueCertificate();
            
            return response()->json([
                'eligible' => true,
                'certificate' => $certificate,
                'message' => 'Congratulations! You have earned your certificate!'
            ]);
        }

        return response()->json([
            'eligible' => false,
            'has_certificate' => $user->certificate ? true : false,
            'certificate' => $user->certificate
        ]);
    }

    /**
     * View certificate
     */
    public function view()
    {
        $user = Auth::user();
        $certificate = $user->certificate;

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
        $certificate = $user->certificate;

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
        $certificate = $user->certificate;

        if (!$certificate) {
            return redirect()->route('user.home')
                ->with('error', 'You have not earned a certificate yet.');
        }

        // Prepare data
        $userName = $user->first_name . ' ' . $user->last_name;
        $certNumber = $certificate->certificate_number;
        $issueDate = $certificate->issued_at->format('F d, Y');
        $lessonsCompleted = $certificate->total_lessons_completed;
        $avgQuizScore = number_format($certificate->average_quiz_score, 2);
        $avgSimScore = number_format($certificate->average_simulation_score, 2);
        
        // Paths
        $templatePath = public_path('img/certificate-template.png');
        $outputPath = storage_path('app/certificates/' . $certNumber . '.pdf');
        $scriptPath = base_path('generate_certificate.py');
        
        // Create certificates directory if it doesn't exist
        if (!file_exists(storage_path('app/certificates'))) {
            mkdir(storage_path('app/certificates'), 0755, true);
        }

        // Check if template exists, if not use the uploaded image
        if (!file_exists($templatePath)) {
            // Try to find the uploaded template
            $uploadedTemplate = public_path('img/1.png');
            if (file_exists($uploadedTemplate)) {
                $templatePath = $uploadedTemplate;
            } else {
                return redirect()->back()->with('error', 'Certificate template not found. Please contact administrator.');
            }
        }

        // Install reportlab if needed
        exec('pip list | grep reportlab', $checkOutput);
        if (empty($checkOutput)) {
            exec('pip install reportlab Pillow --break-system-packages 2>&1', $installOutput);
        }

        // Generate PDF using Python script
        $command = sprintf(
            'python3 %s %s %s %s %s %s %s %s %s',
            escapeshellarg($scriptPath),
            escapeshellarg($outputPath),
            escapeshellarg($userName),
            escapeshellarg($certNumber),
            escapeshellarg($issueDate),
            escapeshellarg($lessonsCompleted),
            escapeshellarg($avgQuizScore),
            escapeshellarg($avgSimScore),
            escapeshellarg($templatePath)
        );

        exec($command . ' 2>&1', $output, $return_var);

        if ($return_var !== 0 || !file_exists($outputPath)) {
            Log::error('Certificate generation failed', [
                'command' => $command,
                'output' => $output,
                'return_var' => $return_var,
                'template_path' => $templatePath,
                'script_path' => $scriptPath
            ]);
            
            return redirect()->back()->with('error', 'Failed to generate certificate PDF. Error: ' . implode(' ', $output));
        }

        // Download the PDF
        return response()->download($outputPath, $certNumber . '.pdf', [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }
}
