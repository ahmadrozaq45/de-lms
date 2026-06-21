<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $appSettings = [];
        if ($user->role === 'admin') {
            $appSettings = Setting::getMany([
                'ai_provider',
                'ai_api_key_anthropic', 'ai_model_anthropic',
                'ai_api_key_gemini',    'ai_model_gemini',
                'ai_api_key_groq',      'ai_model_groq',
                'ai_api_key_openai',    'ai_model_openai',
                'theme_color', 'theme_mode',
                'lp_title', 'lp_subtitle', 'lp_show_courses',
                'cert_enabled', 'cert_issuer_name', 'cert_footer_text',
            ]);
        }

        return view('settings.index', compact('user', 'appSettings'));
    }

    // ── Profil ──────────────────────────────────────────────────────────────

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($data);
        return redirect()->route('settings.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // ── Password ─────────────────────────────────────────────────────────────

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('settings.index')
            ->with('success', 'Password berhasil diperbarui.');
    }

    // ── Hapus Akun ───────────────────────────────────────────────────────────

    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required']);
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Password tidak sesuai.'])
                ->with('tab', 'danger');
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }

    // ── Admin: API Setting ───────────────────────────────────────────────────

    public function updateApi(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'ai_provider'          => 'required|in:anthropic,gemini,groq,openai',
            'ai_api_key_anthropic' => 'nullable|string|max:500',
            'ai_api_key_gemini'    => 'nullable|string|max:500',
            'ai_api_key_groq'      => 'nullable|string|max:500',
            'ai_api_key_openai'    => 'nullable|string|max:500',
            'ai_model_anthropic'   => 'required|string|max:100',
            'ai_model_gemini'      => 'required|string|max:100',
            'ai_model_groq'        => 'required|string|max:100',
            'ai_model_openai'      => 'required|string|max:100',
        ]);

        Setting::set('ai_provider', $request->ai_provider);

        foreach (['anthropic', 'gemini', 'groq', 'openai'] as $provider) {
            Setting::set("ai_api_key_{$provider}", $request->input("ai_api_key_{$provider}", ''));
            Setting::set("ai_model_{$provider}",   $request->input("ai_model_{$provider}", ''));
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan API berhasil disimpan.')
            ->with('tab', 'api');
    }

    // ── Admin: Theme Setting ─────────────────────────────────────────────────

    public function updateTheme(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'theme_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_mode'  => 'required|in:light,dark',
        ]);

        Setting::set('theme_color', $request->theme_color);
        Setting::set('theme_mode',  $request->theme_mode);

        return redirect()->route('settings.index')
            ->with('success', 'Tema berhasil diperbarui.')
            ->with('tab', 'theme');
    }

    // ── Admin: Landing Page Setting ──────────────────────────────────────────

    public function updateLandingPage(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'lp_title'        => 'required|string|max:120',
            'lp_subtitle'     => 'nullable|string|max:300',
            'lp_show_courses' => 'nullable',
        ]);

        Setting::set('lp_title',        $request->lp_title);
        Setting::set('lp_subtitle',     $request->lp_subtitle ?? '');
        Setting::set('lp_show_courses', $request->has('lp_show_courses') ? '1' : '0');

        return redirect()->route('settings.index')
            ->with('success', 'Setting landing page berhasil disimpan.')
            ->with('tab', 'landingpage');
    }

    // ── Admin: Certificate Setting ───────────────────────────────────────────

    public function updateCertificate(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'cert_issuer_name' => 'required|string|max:120',
            'cert_footer_text' => 'nullable|string|max:300',
            'cert_enabled'     => 'nullable',
        ]);

        Setting::set('cert_enabled',     $request->has('cert_enabled') ? '1' : '0');
        Setting::set('cert_issuer_name', $request->cert_issuer_name);
        Setting::set('cert_footer_text', $request->cert_footer_text ?? '');

        return redirect()->route('settings.index')
            ->with('success', 'Setting sertifikat berhasil disimpan.')
            ->with('tab', 'certificate');
    }

    // ── Teacher: Preferensi Provider AI ─────────────────────────────────────

    public function updateAiPreference(Request $request)
    {
        $this->authorizeTeacher();

        $request->validate([
            'preferred_ai_provider' => 'required|string|in:anthropic,gemini,groq,openai',
        ]);

        $user = Auth::user();

        // Pastikan provider yang dipilih memang sudah diisi key oleh admin.
        $hasKey = trim((string) Setting::get("ai_api_key_{$request->preferred_ai_provider}", '')) !== '';
        if (!$hasKey) {
            return redirect()->route('settings.index')
                ->withErrors(['preferred_ai_provider' => 'Provider yang dipilih belum diisi API key oleh admin.'])
                ->with('tab', 'ai-preference');
        }

        $user->update(['preferred_ai_provider' => $request->preferred_ai_provider]);

        return redirect()->route('settings.index')
            ->with('success', 'Preferensi provider AI berhasil disimpan.')
            ->with('tab', 'ai-preference');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function authorizeAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeTeacher(): void
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Akses ditolak.');
        }
    }
}