<p>{{ $isNewLead ? 'A new lead was submitted through the public inquiry form.' : 'An existing client/lead was updated from the public inquiry form.' }}</p>

<p><strong>Record</strong><br>
Reference: {{ $record->client_id ?? 'N/A' }}<br>
Type: {{ $record->type ?? 'N/A' }}<br>
ID: {{ $record->id }}</p>

<p><strong>Submitted details</strong><br>
Name: {{ $submitted['name'] }}<br>
Phone: {{ $submitted['phone'] }}<br>
Email: {{ $submitted['email'] }}<br>
Visa subclass: {{ $submitted['visa_subclass'] ?? '—' }}<br>
Address: {{ $submitted['address'] ?? '—' }}</p>

<p><strong>Current record (summary)</strong><br>
Name: {{ trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? '')) }}<br>
Phone: {{ $record->phone ?? '—' }}<br>
Email: {{ $record->email ?? '—' }}<br>
Visa (visa_type): {{ $record->visa_type ?? '—' }}<br>
Address: {{ $record->address ?? '—' }}<br>
manual_form_fill: {{ $record->manual_form_fill ?? 0 }}</p>
