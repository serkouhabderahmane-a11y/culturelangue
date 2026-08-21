<?php
// Shim for mb_strimwidth (not provided by symfony/polyfill-mbstring).
// The real mbstring extension is blocked on this machine by an OS
// Application Control policy, so we polyfill the one missing function
// that Termwind needs. All other mb_* functions are provided by the
// symfony polyfill loaded through composer.
if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $string, int $start, int $width, string $trimmarker = '', ?string $encoding = null): string
    {
        $encoding = $encoding ?: mb_internal_encoding();
        $string = (string) mb_substr($string, $start, null, $encoding);
        if (mb_strlen($string, $encoding) <= $width) {
            return $string;
        }
        $width = max(0, $width - mb_strlen($trimmarker, $encoding));
        return rtrim(mb_substr($string, 0, $width, $encoding)) . $trimmarker;
    }
}
