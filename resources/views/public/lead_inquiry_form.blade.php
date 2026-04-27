@extends('layouts.public-minimal')

@section('content')
    <h1>Lead/Client Information Form</h1>

    @if (session('success'))
        <div class="msg ok">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="msg info">{{ session('info') }}</div>
    @endif
    @if ($errors->any() && ! $errors->has('form'))
        <div class="msg err">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif
    @if ($errors->has('form'))
        <div class="msg err">{{ $errors->first('form') }}</div>
    @endif

    @if (!empty($pendingConfirm))
        @php
            $ed = $pendingConfirm['existing_display'] ?? [];
            $pr = $pendingConfirm['proposed'] ?? [];
            $ps = $pendingConfirm['proposed_display'] ?? $pr;
        @endphp
        <p class="lead">
            Thanks for providing details. Your details already match a record in our system. Below is what we currently have on file.
            If you want to update this record with the information you just entered, press <strong>Confirm</strong>.
            If you do not want to make any changes, press <strong>Cancel</strong>.
        </p>

        <h2 style="font-size:1.05rem; color:var(--b); margin:1.25rem 0 .5rem;">Current details in our system</h2>
        <dl class="details-dl">
            <dt>Name</dt>
            <dd>{{ $ed['name'] ?? '—' }}</dd>
            <dt>Email</dt>
            <dd>{{ $ed['email'] ?? '—' }}</dd>
            <dt>Phone</dt>
            <dd>{{ $ed['phone'] ?? '—' }}</dd>
            <dt>Visa subclass</dt>
            <dd>{{ $ed['visa_subclass'] ?? '—' }}</dd>
            <dt>Address</dt>
            <dd>{{ $ed['address'] ?? '—' }}</dd>
        </dl>

        <h2 style="font-size:1.05rem; color:var(--b); margin:1.25rem 0 .5rem;">What you just submitted (will replace the above if you confirm)</h2>
        <dl class="details-dl">
            <dt>Name</dt>
            <dd>{{ $ps['name'] ?? '—' }}</dd>
            <dt>Email</dt>
            <dd>{{ $ps['email'] ?? '—' }}</dd>
            <dt>Phone</dt>
            <dd>{{ $ps['phone'] ?? '—' }}</dd>
            <dt>Visa subclass</dt>
            <dd>{{ $ps['visa_subclass'] ?? '—' }}</dd>
            <dt>Address</dt>
            <dd>{{ $ps['address'] ?? '—' }}</dd>
        </dl>

        <div class="actions-row" style="display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1.5rem;">
            <form method="post" action="{{ $confirmAction }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-confirm" style="background:var(--b); color:#fff; border:0; padding:.75rem 1.4rem; font-weight:600; border-radius:8px; cursor:pointer;">Confirm update</button>
            </form>
            <form method="post" action="{{ $cancelAction }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-cancel" style="background:#6c757d; color:#fff; border:0; padding:.75rem 1.4rem; font-weight:600; border-radius:8px; cursor:pointer;">Cancel</button>
            </form>
        </div>
    @else
        <p class="lead">Please fill in your details. Fields marked with <span class="req">*</span> are required.</p>

        <form method="post" action="{{ $formAction }}">
            @csrf
            <div>
                <label for="name">Name <span class="req">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" maxlength="255">
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="phone">Phone <span class="req">*</span></label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required autocomplete="tel" maxlength="255">
                @error('phone')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" maxlength="255">
                @error('email')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="visa_subclass">Visa subclass</label>
                <input type="text" name="visa_subclass" id="visa_subclass" value="{{ old('visa_subclass') }}" maxlength="255" placeholder="e.g. 482, 190">
                @error('visa_subclass')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="address">Address</label>
                <textarea name="address" id="address" maxlength="2000" placeholder="Optional">{{ old('address') }}</textarea>
                @error('address')<div class="err">{{ $message }}</div>@enderror
            </div>
            <button type="submit">Submit</button>
        </form>
    @endif
@endsection
