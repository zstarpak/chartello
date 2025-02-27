# Chartello

[![tests](https://github.com/chartello/chartello/actions/workflows/tests.yml/badge.svg)](https://github.com/chartello/chartello/actions/workflows/tests.yml)
[![GitHub license](https://img.shields.io/github/license/chartello/chartello)](https://github.com/chartello/chartello/blob/master/LICENSE.md)

Chartello provides a simple UI for visualizing your data in your Laravel-powered app.

<a href="https://demo.chartello.com/">
    <img width="800" alt="screenshot" src="https://user-images.githubusercontent.com/10030505/219040779-909f08f8-c068-4d30-944a-2f64e92159e2.png">
</a>

[Demo →](https://demo.chartello.com/)

## Installation

Add the package to your project via composer:
```bash
composer require chartello/chartello
```

Publish the package resources:

```bash
php artisan chartello:install
```

Run new migrations: 

```bash
php artisan migrate
```

Voilà! Open `yourapp.test/chartello` on your local machine. You should see the Chartello dashboard.

## Configuration

### Dashboard Authorization 

Chartello dashboards allow users to write SQL queries against your database. This makes it important for 
you to ensure that they are protected behind appropriate permissions.

When you install Chartello, it adds a `app\Http\Middleware\ProtectChartello` middleware to your app. This,
by default, limits the access to these dashboards only to your local environment. You should modify this 
middleware to define how access to these dashboards should be restricted in your production environment.

```php
/**
 * Define the authorization logic for accessing Chartello.
 *
 * @param $request
 * @return bool
 */
protected function authorize($request)
{
    if (App::environment('local')) {
        return true;
    }

    return $request->user() && in_array($request->user()->email, [
        //
    ]);
}
```

### Dashboard Routes

After installing Chartello, its configuration file will be located at `config/chartello.php`. This file allows you to
customize the `path` to the Chartello dashboards and adjust HTTP middleware that should be used when serving them.

## Usage

### Writing Queries

Chartello currently supports two types of panels: 

#### 1. Trend Charts
A trend chart expects an `x` column with date/datetime values and a numeric `y` column. 

To populate a trend chart, create a new panel in 
the UI and enter a query similar to this one:

```sql
SELECT DATE(created_at) AS x, COUNT(*) AS y
FROM users
WHERE created_at BETWEEN @start AND @end
GROUP BY x
ORDER BY x ASC
```

Including the `BETWEEN @start AND @end` filter ensures that your
chart reflects the date range selection available in the UI.  

#### 2. Tables

Tables are flexible and accept almost any `SELECT` queries.

If you wish the data in your table to reflect the date selection from the UI, 
you should include the `BETWEEN @start AND @end` filter in your query.

Here is an example query for populating a table panel:

```sql
SELECT name, email, created_at
FROM users
WHERE created_at BETWEEN @start AND @end
ORDER BY created_at DESC
LIMIT 5
```

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
