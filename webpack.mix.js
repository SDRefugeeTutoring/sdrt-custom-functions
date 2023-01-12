let mix = require('laravel-mix');

mix.ts('src/VolunteerPortal/resources/js/App.tsx', 'volunteer-portal.js')
    .ts('src/Reports/resources/js/App.tsx', 'reports.js')
    .sourceMaps(false, 'source-map')
    .react()
    .version()
    .setPublicPath('assets');

mix.options({
    terser: {
        extractComments: false,
    },
});
