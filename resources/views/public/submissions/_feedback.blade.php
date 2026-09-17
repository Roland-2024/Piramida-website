@if (session('success'))<p role="status" class="template-success">{{ session('success') }}</p>@endif
@if ($errors->any())<div role="alert" class="template-errors"><p>{{ __('cms.correct_form') }}</p><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
