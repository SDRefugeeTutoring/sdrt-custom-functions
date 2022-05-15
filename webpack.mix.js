let mix = require('laravel-mix');

mix.ts('src/VolunteerPortal/resources/js/App.tsx', 'volunteer-portal.js')
    .sourceMaps(false, 'source-map')
    .react()
    .setPublicPath('assets');
