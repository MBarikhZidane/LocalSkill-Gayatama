<x-guest-layout>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 mx-auto max-w-md dark:border-slate-800 dark:bg-slate-900 transition-colors duration-200">
        <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">STEP 2 OF 2</p>
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Complete your profile</h1>

        <form method="POST" action="{{ route('complete-profile.store') }}" class="grid gap-4 mt-6">
            @csrf

            <!-- University -->
            <div>
                <label for="university_id" class="grid gap-2 text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">University</label>
                <select id="university_id" name="university_id"
                    class="select select-bordered w-full bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 @error('university_id') select-error @enderror">
                    <option value="" class="bg-white text-slate-900 dark:bg-slate-800 dark:text-white">Select your university (Opsional)</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" class="bg-white text-slate-900 dark:bg-slate-800 dark:text-white" {{ old('university_id', auth()->user()->university_id) == $university->id ? 'selected' : '' }}>
                            {{ $university->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('university_id')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="grid gap-2 text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Phone Number (WhatsApp)</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                    placeholder="e.g. 08123456789"
                    class="input input-bordered w-full bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 @error('phone') input-error @enderror">
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Bio -->
            <div>
                <label for="bio" class="grid gap-2 text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Short Bio (Opsional)</label>
                <textarea id="bio" name="bio" rows="3"
                    placeholder="Tell other students about your skills or interests..."
                    class="textarea textarea-bordered w-full bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 @error('bio') textarea-error @enderror">{{ old('bio', auth()->user()->bio) }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full">
                <button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">
                    Save & Continue
                </button>

                <a href="{{ route('explore.index') }}"
                    class="btn w-full border-2 border-emerald-600 bg-white text-emerald-600 hover:bg-emerald-700 hover:text-white dark:bg-slate-800 dark:text-emerald-400 dark:border-emerald-500 dark:hover:bg-emerald-600 dark:hover:text-white">
                    Lewati
                </a>
            </div>
        </form>
    </section>
</x-guest-layout>