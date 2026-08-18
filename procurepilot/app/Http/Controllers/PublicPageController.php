<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function about()
    {
        return view('public.about');
    }

    public function features()
    {
        return view('public.features');
    }

    public function howItWorks()
    {
        return view('public.how-it-works');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function help()
    {
        return view('public.help');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'topic'   => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Log contact messages to a file for the demo (no mail driver configured).
        \Illuminate\Support\Facades\Log::channel('stack')->info('contact_message', $data);

        return back()->with('contact_sent', true);
    }
}
