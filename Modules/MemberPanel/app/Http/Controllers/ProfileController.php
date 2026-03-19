<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    /**
     * Display the member profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['role', 'relationships.relatedUser']);

        $pendingInvitesCount = \App\Models\UserRelationship::where('related_user_id', $user->id)->pending()->count();

        return view('memberpanel::profile.show', [
            'user' => $user,
            'pendingInvitesCount' => $pendingInvitesCount,
        ]);
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('memberpanel::profile.edit', compact('user'));
    }

    /**
     * Update the member profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        \Log::info('Profile Update Attempted', ['user_id' => $user->id, 'has_files' => $request->hasFile('photos')]);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'marital_status' => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|max:10',
            'title' => 'nullable|string|max:100',
            'is_ordained' => 'boolean',
            'ordination_date' => 'nullable|date',
            'ministry_start_date' => 'nullable|date',
            'affiliated_church' => 'nullable|string|max:255',
            'baptist_convention' => 'nullable|string|max:255',
            'theological_education' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'is_baptized' => 'nullable|boolean',
            'baptism_date' => 'nullable|date',
            'profession' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:50',
            'workplace' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'photos.*' => 'nullable|image|max:8192',
        ]);

        // Gera nome completo
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        // Garante que is_baptized e is_ordained sejam booleanos
        $validated['is_baptized'] = $request->has('is_baptized');
        $validated['is_ordained'] = $request->has('is_ordained');

        // Hash da senha se fornecida
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Upload de múltiplas fotos (limite 3 total)
        if ($request->hasFile('photos')) {
            $user->load('profilePhotos');
            $currentPhotoCount = $user->profilePhotos->count();
            $files = $request->file('photos');

            foreach ($files as $file) {
                if ($currentPhotoCount < 3) {
                    $path = $file->store('users/photos', 'public');

                    $user->profilePhotos()->create([
                        'path' => $path,
                        'is_active' => $currentPhotoCount === 0 && ! $user->profilePhotos()->where('is_active', true)->exists(),
                    ]);

                    $currentPhotoCount++;
                }
            }

            // Sincroniza foto principal se houver foto ativa
            $activePhoto = $user->profilePhotos()->where('is_active', true)->first();
            if ($activePhoto) {
                $user->photo = $activePhoto->path;
                $user->save();
            }
        }

        // Remove campos que não devem ser salvos diretamente na tabela users
        unset($validated['password_confirmation']);
        unset($validated['photos']);
        // Proteger colunas EBD (xp/level) – nunca sobrescrever no update de perfil
        unset($validated['xp'], $validated['level']);

        // Atualiza o usuário e garante a persistência da sessão
        $user->fill($validated);
        $user->save();

        // Marcar para o CBAV Bot exibir parabéns ao completar perfil na próxima página
        $user->refresh();
        if ($user->getProfileCompletionPercentage() >= 80) {
            session(['profile_just_completed' => true]);
        }

        return redirect()->route('memberpanel.profile.show')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Define uma foto como ativa
     */
    public function setActivePhoto(UserPhoto $photo)
    {
        $user = Auth::user();

        if ($photo->user_id !== $user->id) {
            abort(403);
        }

        // Desativa todas
        $user->profilePhotos()->update(['is_active' => false]);

        // Ativa a selecionada
        $photo->update(['is_active' => true]);

        // Sincroniza no User
        $user->update(['photo' => $photo->path]);

        return back()->with('success', 'Foto de perfil alterada com sucesso!');
    }

    /**
     * Remove uma foto
     */
    public function deletePhoto(UserPhoto $photo)
    {
        $user = Auth::user();

        if ($photo->user_id !== $user->id) {
            abort(403);
        }

        // Remove do storage
        Storage::disk('public')->delete($photo->path);

        // Se era a ativa, limpa o User->photo
        if ($photo->is_active) {
            $user->update(['photo' => null]);

            // Tenta ativar outra
            $newActive = $user->profilePhotos()->where('id', '!=', $photo->id)->first();
            if ($newActive) {
                $newActive->update(['is_active' => true]);
                $user->update(['photo' => $newActive->path]);
            }
        }

        $photo->delete();

        return back()->with('success', 'Foto removida com sucesso!');
    }
}
