@props(['url'])
<tr>
    <td class="header" align="center">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ asset(config('jago24.logo')) }}" class="logo" width="220" alt="জাগো২৪বার্তা">
        </a>
        <p style="margin: 10px 0 0; color: #056738;">{{ config('jago24.slogan') }}</p>
    </td>
</tr>
