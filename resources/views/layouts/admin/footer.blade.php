<footer class="footer text-center">
    <script>document.write(new Date().getFullYear())</script>
{{--    © {{ App\Models\GeneralSetting::getSiteSettingValue(Auth::id(), 'SITE_NAME') ?? Auth::user()->name }}.--}}
    &nbsp;&nbsp;© VISITAR
    Panel Version {{ config('constants.panel_version') }}
</footer>
