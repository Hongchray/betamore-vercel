<!-- ABOUT THE PROJECT -->

### BETAMORE ADMIN

## Project's features:

- Users Management & Roles + Permissions 
- Orders Tracking
- Manage Products
- Manage Brands
- Manage Categories
- Online Payment

### Built With

This section should list any major frameworks/libraries

- Laravel Inertia JS
- Vue JS - Typescript
- Tailwindcss
- Shadcn-vue UI

<!-- GETTING STARTED -->

## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

### Installation

_Below is an example of how you can instruct your audience on installing and setting up your app. This template doesn't rely on any external dependencies or services._

1. Clone the repo
    ```sh
    git clone git@github.com:Focuz-Solution/betamore-admin.git
    ```
2. Install NPM packages
    ```sh
    npm install
    ```
3. Install Composer packages
    ```sh
    composer install
    ```
4. Make .env file
    ```sh
    cp .env.example .env
    ```
5. Migration and seeder
    ```sh
    php artisan migrate:fresh --seed
    ```
6. Generate Key
    ```sh
    php artisan key:generate
    ```
7. Run Project
    ```sh
    composer run dev
    ```
8. Run Queue Service (Horizon Radis)

    ```sh
    php artisan horizon
    ```

9. If the framework folder is missing, you can manually create the required structure:
    ```sh
    mkdir -p storage/framework/{sessions,views,cache}
    ```

### Rule Before Git Push

Before pushing your code to the repository, ensure that your application is functioning correctly and free of errors. This includes running type checks and testing to catch any potential issues.

1. Before you push your changes, ensure that everything is functioning correctly. Run your application locally and test the core features to confirm they behave as expected.

2. Check Types

- Use TypeScript to check for type errors in your project. Run the following command:
    ```sh
     npm run type-check
    ```

## Acknowledgments

Use this space to list resources you find helpful and would like to give credit to. I've included a few of my favorites to kick things off!

- [Laravel Ineria](https://choosealicense.com)
- [Vue JS](https://vuejs.org)
- [Tailwindcss](https://tailwindcss.com)
- [Shadcn-vue UI](https://www.shadcn-vue.com/)
- [Radix UI-The Primitive API Reference of Shadcn-vue](https://www.radix-vue.com/)
- [Pinia-The intuitive store for Vue.js](https://pinia.vuejs.org)
- [Lucide Vue Next-The Icon](https://lucide.dev/guide/packages/lucide-vue-next)

AI supports for Developer!

- [Chat GPT](https://choosealicense.com)
- [Claude AI](https://claude.ai/new)
- [V0 Dev- Frontend AI](https://v0.dev/)

## Credentials

```sh
   hello@focuzsolution.com
   iamfocuz
```
