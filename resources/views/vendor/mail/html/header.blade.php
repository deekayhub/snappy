@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset(setting('site_logo', 'assets/images/snappy-logo.png')) }}" class="logo" alt="{{ setting('app_name', config('app.name')) }}" style="height: auto; width: auto; max-height: 60px;">
</a>
</td>
</tr>
