module.exports = {
    content: [
        './templates/**/*.twig',
        './assets/**/*.{js,ts,vue,jsx,tsx}',
        // if you use Symfony form themes:
        './vendor/symfony/twig-bridge/Resources/views/Form/**/*.html.twig',
        // if you use Flowbite:
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin'),
    ],
}
