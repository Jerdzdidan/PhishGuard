<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
