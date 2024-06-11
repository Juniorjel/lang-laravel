<!DOCTYPE html>
<html>

<head>
    <title>Translation Management</title>
</head>

<body>
    <h1>Translation Management</h1>

    @if(session('success'))
    <div style="color: green;">
        {{ session('success') }}
    </div>
    @endif

    <form method="GET" action="{{ route('translations.index') }}">
        <label for="file">Select Translation File:</label>
        <select name="file" id="file" onchange="this.form.submit()">
            <option value="">-- Select File --</option>
            @foreach($files as $file)
            <option value="{{ $file }}" {{ request('file') === $file ? 'selected' : '' }}>{{ $file }}</option>
            @endforeach
        </select>
    </form>

    @if($selectedFile)
    <form method="POST" action="{{ route('translations.update') }}">
        @csrf
        <input type="hidden" name="file" value="{{ $selectedFile }}">

        <table border="1">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>English</th>
                    <th>Nepali</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flattenedSourceTranslations as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>
                        <input type="text" name="translations[{{ $key }}][en]" value="{{ $value }}">
                    </td>
                    <td>
                        <input type="text" name="translations[{{ $key }}][np]" value="{{ $flattenedTargetTranslations[$key] ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

        <button type="submit">Update Translations</button>
    </form>
    @endif
</body>

</html>