<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicCertificateController extends Controller
{
    /**
     * Public, no-auth verification page — anyone with the certificate
     * number or a scanned QR code lands here.
     */
    public function verify(Request $request, ?string $code = null)
    {
        $code = $code ?? $request->input('code');
        $certificate = Certificate::with(['user', 'course'])
            ->where('verification_code', $code)
            ->orWhere('certificate_number', $code)
            ->first();

        return view('Guest.certificate-verify', compact('certificate', 'code'));
    }

    /**
     * Student-facing certificate list.
     */
    public function myCertificates()
    {
        $user = Auth::guard('student')->user();

        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get();

        return view('Users.User.certificates', compact('certificates'));
    }

    /**
     * View (and, if dompdf is installed, download as PDF) a single
     * certificate. Only the certificate's own owner can view/download it
     * this way — the public verification page above is the one anyone can
     * reach, and it deliberately shows less (no download).
     */
    public function show($id)
    {
        $user = Auth::guard('student')->user();

        $certificate = Certificate::with(['user', 'course'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Guest.certificate-pdf', compact('certificate'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($certificate->certificate_number . '.pdf');
        }

        // dompdf not installed yet — fall back to a print-friendly HTML
        // page. The browser's own "Print > Save as PDF" produces an
        // equivalent result without requiring the package.
        return view('Guest.certificate-pdf', compact('certificate'));
    }
}
