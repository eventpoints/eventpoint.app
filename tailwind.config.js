module.exports = {
    content: [
        './templates/**/*.{html,twig}',
        './assets/**/*.{js,ts,vue,jsx,tsx}',
        // if you use Symfony form themes:
        './vendor/symfony/twig-bridge/Resources/views/Form/**/*.html.twig',
        // if you use Flowbite:
        './node_modules/flowbite/**/*.js',
    ],
    safelist: [
        // Ensure primary color utilities are always generated
        'bg-primary-600',
        'bg-primary-700',
        'hover:bg-primary-700',
        'text-primary-600',
        'border-primary-600',
        'focus:ring-primary-300',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin'),
    ],
}
