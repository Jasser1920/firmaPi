{
    "runTerminalCommand.commands": [
    
        {
            "command": "symfony new project_test --version='6.4.*' --webapp ",
            "name": "Create new project Symfony with CLI"
        },
        {
            "command": "composer create-project symfony/skeleton my_project_name",
            "name": "Create new project Symfony Using Composer"
        },
        {
            "command": "composer require twig",
            "name": "Twig"
        },

        {
            "command": "composer require doctrine",
            "name": "Doctrine"
        },
        {
            "command": "php bin/console doctrine:database:create",
            "name": "Create new Database"
        },
        {
            "command": "php bin/console make:controller HomeController",
            "name": "Create new Controller"
        },
        {
            "command": "php bin/console make:entity Product",
            "name": "Generate an Entity"
        },
        
        {
            "command": "php bin/console make:migration",
            "name": "Tracks entity changes and generates a migration file."
        },
        {
            "command": "php bin/console doctrine:migrations:migrate",
            "name": "Applies migrations to the database."
        },
        {
            "command": "php bin/console make:form ProductType",
            "name": "Generate a Form Class"
        },
    ],
    "terminal.integrated.autoReplies": {
        
    },
    "powermode.combo.location": "editor",
    "powermode.combo.counterEnabled": "show",
    "powermode.presets": "clippy"
}