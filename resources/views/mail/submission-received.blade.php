<h1>New {{ $submission->type->label() }}</h1>
<p><strong>Name:</strong> {{ $submission->name }}</p>
<p><strong>Email:</strong> {{ $submission->email }}</p>
@if ($submission->phone)<p><strong>Phone:</strong> {{ $submission->phone }}</p>@endif
@if ($submission->subject)<p><strong>Subject:</strong> {{ $submission->subject }}</p>@endif
@if ($submission->message)<p>{!! nl2br(e($submission->message)) !!}</p>@endif
<p>Review request #{{ $submission->id }} in the Piramida administration dashboard.</p>
