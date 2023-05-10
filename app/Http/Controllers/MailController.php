<?php

namespace App\Http\Controllers;

use App\Mail\ChangePassword;
use App\Mail\HelloMail;
use App\Mail\NewCategory;
use App\Mail\RefuseEstablishmentPro;
use App\Mail\RefuseEvent;
use App\Mail\RefusePro;
use App\Mail\ValideEstablishmentPro;
use App\Mail\ValideEvent;
use App\Mail\ValidePro;
use App\Mail\WelcomeBarathonien;
use App\Mail\WelcomePro;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailController extends Controller
{
    use HttpResponses;
    private const MAIL_RETURN_MESSAGE = "MAIL SEND";
    public function hello()
    {
        Mail::to("barathon.m2i@gmail.com")->send(new HelloMail("test"));

        return $this->success(null, "MAIL SEND");
    }

    public function welcomePro($id)
    {

        $user = User::findOrFail($id);

        Mail::to($user->email)->send(new WelcomePro($user));

        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    public function welcomeBarathonien($id)
    {

        $user = User::findOrFail($id);

        Mail::to($user->email)->send(new WelcomeBarathonien($user));

        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
            ],
        ], [
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail n'est pas valide.",
        ]);
       
        $user = User::where('email', $request->input('email'))->firstOrFail();
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);

        $user->save();

        Mail::to($user->email)->send(new ChangePassword($user, $newPassword));
        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    public function statusPro($id, $status)
    {

        $user = User::findOrFail($id);

        if ($status == '0') {
            Mail::to($user->email)->send(new ValidePro($user));
        } elseif ($status == '1') {
            Mail::to($user->email)->send(new RefusePro($user));
        }

        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    public function statusEstablishmentPro($id, $status)
    {
        $establishment = Establishment::findOrFail($id);
        $owner = $establishment->owner;
        $user = $owner->users[0];

        if ($status == '0') {
            Mail::to($user->email)->send(new ValideEstablishmentPro($user, $establishment));
        } elseif ($status == '1') {
            Mail::to($user->email)->send(new RefuseEstablishmentPro($user, $establishment));
        }

        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    public function statusEventPro($id, $status)
    {
        $event = Event::findOrFail($id);
        $user = $event->users;

        if ($status == '0') {
            Mail::to($user->email)->send(new ValideEvent($user, $event));
        } elseif ($status == '1') {
            Mail::to($user->email)->send(new RefuseEvent($user, $event));
        }

        return $this->success(null, self::MAIL_RETURN_MESSAGE);
    }

    /**
     * Send mail to administrator
     */
    public function sendMailNewCategory(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'category_name' => 'required|string',
            'category_visibility' => 'required|string',
        ], [
            'category_name.required' => 'Le nom de la catégorie est requis',
            'category_name.string' => 'Le nom de la catégorie doit être une chaine de caractères',
            'category_visibility.required' => 'La visibilité de la catégorie est requise',
            'category_visibility.string' => 'La visibilité de la catégorie doit être une chaine de caractères',
        ]);
        $categoryName = $request->input('category_name');
        $categoryVisibility = $request->input('category_visibility');

        $mail = new NewCategory($user, $categoryName, $categoryVisibility);
        Mail::to('barathon.m2i@gmail.com')->send($mail);

        return $this->success(null, "Demande envoyée avec succès");
    }
}
