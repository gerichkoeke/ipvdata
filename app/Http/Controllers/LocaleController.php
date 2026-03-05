<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LocaleController extends Controller
{
    protected array $supported = ['pt_BR', 'en', 'es'];
    public function switch(Request $request)
    {
        $locale = $request->input('locale', config('app.locale'));
        if (!in_array($locale, $this->supported, true)) {
            $locale = config('app.locale', 'pt_BR');
        }
        $request->session()->put('locale', $locale);
        if (Auth::check()) {
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();
        }
        return redirect($request->input('redirect', url()->previous()));
    }
}
