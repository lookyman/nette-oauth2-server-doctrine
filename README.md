Lookyman/NetteOAuth2Server Doctrine
===================================

Integration of [The League of Extraordinary Packages](https://thephpleague.com)' [OAuth 2.0 Server](https://oauth2.thephpleague.com) into [Nette Framework](https://nette.org) - [Kdyby/Doctrine](https://github.com/Kdyby/Doctrine) storage implementation.

[![Build Status](https://travis-ci.org/lookyman/nette-oauth2-server-doctrine.svg?branch=master)](https://travis-ci.org/lookyman/nette-oauth2-server-doctrine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lookyman/nette-oauth2-server-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lookyman/nette-oauth2-server-doctrine/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/lookyman/nette-oauth2-server-doctrine/badge.svg?branch=master)](https://coveralls.io/github/lookyman/nette-oauth2-server-doctrine?branch=master)
[![Downloads](https://img.shields.io/packagist/dt/lookyman/nette-oauth2-server-doctrine.svg)](https://packagist.org/packages/lookyman/nette-oauth2-server-doctrine)
[![Latest stable](https://img.shields.io/packagist/v/lookyman/nette-oauth2-server-doctrine.svg)](https://packagist.org/packages/lookyman/nette-oauth2-server-doctrine)


Installation
------------

### 0. The boring part

Read [this](https://oauth2.thephpleague.com). All of it. Seriously, don't just skip it and then come back complaining that something doesn't work.

Don't forget to install and configure [Kdyby/Doctrine](https://github.com/Kdyby/Doctrine) if you haven't already.

### 1. Install using [Composer](https://getcomposer.org/):

```sh
composer require lookyman/nette-oauth2-server-doctrine
```

### 2. Setup routes

Depending on which grants you want to support, you will have to setup routes to either `access_token`, `authorize`, or both endpoints.

- For grants other than `Implicit` setup the `access_token` endpoint route.
- For `Authorization Code` or `Implicit` grants setup the `authorize` endpoint route.

The endpoints are located at `NetteOAuth2Server:OAuth2:accessToken` and `NetteOAuth2Server:OAuth2:authorize` mapping respectively, and the setup should look something like this:

```php
class RouterFactory
{
    /**
     * @return IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList();
        $router[] = new Route('oauth2/<action>', 'NetteOAuth2Server:OAuth2:default');
        // ...
        return $router;
    }
}
```

You can then access those endpoints via `https://myapp.com/oauth2/access-token` and `https://myapp.com/oauth2/authorize` URLs respectively.

### 3. Config

```neon
extensions:
    oauth2: Lookyman\NetteOAuth2Server\Storage\Doctrine\NetteOAuth2ServerDoctrineExtension
    
oauth2:
    grants:
        authCode: on
        clientCredentials: on
        implicit: on
        password: on
        refreshToken: on
    privateKey: /path/to/private.key
    publicKey: /path/to/public.key
    approveDestination: Approve:
    loginDestination: Sign:in
    tablePrefix: nette_oauth2_server_
```

The `grants` section contains grants that you want to enable. By default they are all disabled, so you just have to enter those you want to use. Each value doesn't have to just be a boolean. You can specify a token TTL like this: `[ttl: PT1H]`. Two of the grants also have additional settings. The `Authorization Code` grant has the `authCodeTtl` option, and the `Implicit` grant has the `accessTokenTtl` option. In each of these cases, the format for specifying the intervals follows the format described [here](https://secure.php.net/manual/en/dateinterval.construct.php).

Next, you're going to need a pair of private/public keys. If you didn't skip step 0 you should know how to do that. If you did, now is the time. Go read it, come back when you have the keys, and enter the paths in the `privateKey` and `publicKey` options. If your private key is protected with a passphrase, specify it like this: `privateKey: [keyPath: /path/to/private.key, passPhrase: passphrase]`

Finally, if you are using either `Authorization Code` or `Implicit` grants, you need to setup the redirect destinations. These should be normal strings you would use in `$presenter->redirect()` method. The `approveDestination` is discussed in detail below in step 5. The `loginDestination` should point to the presenter/action where your application has it's login form.

You can omit `approveDestination` and `loginDestination` options if you are not using `Authorization Code` or `Implicit` grants.

The `tablePrefix` option lets you set the prefix for generated SQL tables. The default value is `nette_oauth2_server_`.

### 4. Update database schema

```sh
php www/index.php orm:schema-tool:update --force
```

You might want to use `--dump-sql` instead of `--force` and run the resulting SQL queries manually. But if your database schema was previously in sync with your mappings, this should be safe.

It will generate 7 new tables in the database:

- `nette_oauth2_server_access_token`
- `nette_oauth2_server_access_token_scope`
- `nette_oauth2_server_auth_code`
- `nette_oauth2_server_auth_code_scope`
- `nette_oauth2_server_client`
- `nette_oauth2_server_refresh_token`
- `nette_oauth2_server_scope`

### 5. Implement traits

The last part (and the most fun one) is to hook this all up into your application. For this there's a bunch of handy traits ready, so the process should be fairly smooth. Also, this step is only necessary if you want to use `Authorization Code` or `Implicit` grants, so if you don't, you are already done. Yay!

First of all, you need to tap into your login process. Use the trait `Lookyman\NetteOAuth2Server\UI\LoginPresenterTrait` in your login presenter and call it's `approveOAuth2Request()` method just after you log the user in, but before you do any redirect. The method checks if the user is logging in because they want to approve an authorization request and redirects them to the correct destination. It should look something like this:

```php
class LoginPresenter extends Presenter
{
    use LoginPresenterTrait;

    // ...

    public function loginFormSuccess(Form $form, $values)
    {
        try {
            $this->getUser()->login($values->email, $values->password);
            $this->approveOAuth2Request();
            $this->redirect('Homepage:');

        } catch (AbortException $e) {
            throw $e;

        } catch (\Exception $e) {
            $form->addError($e->getMessage());
        }
    }
}
```

Next, you will have to create an approve presenter. Remember that `approveDestination` option in config? This is where it comes to play. The presenter should use the `Lookyman\NetteOAuth2Server\UI\ApprovePresenterTrait` trait and call it's `initializeApproveComponent()` method in the action the `approveDestination` option leads to. It should look something like this:

```php
class ApprovePresenter extends Presenter
{
    use ApprovePresenterTrait;
    
    // ...

    public function actionDefault()
    {
        $this->initializeApproveComponent();
    }
}
```

Of course, you don't have to create a new presenter just for this. If you want, use the trait in one of your existing ones. Just make sure to set the correct `approveDestination` in the config and call the `initializeApproveComponent()` method.

Finally, that action needs a template. So create a [Latte](https://latte.nette.org) template file in the correct destination for the presenter's action to pick it up, and put a single line somewhere into it:

```latte
{control approve}
```

As you can see, this whole process is highly configurable. The redirection from the login presenter is done with a single line of code, so is the initialization of the approve component, the template can reflect the site's design, etc... This is done to let you have a complete control over your application, and just leave the hard work to this package.


Finalizing the setup
--------------------

This package does not provide ways to manage client applications, access tokens, or scopes. You have to implement those yourself. You can, however, use the entities and repositories provided by this package.

- AccessToken
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity`
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository`
- AuthCode
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity`
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository`
- Client
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity`
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository`
- RefreshToken
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity`
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository`
- Scope
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity`
    - `Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository`

At minimum, you should create a way to register the client applications. Unless of course you just want do it manually in the database.


Protecting resources
--------------------

This package provides an abstract `Lookyman\NetteOAuth2Server\UI\ResourcePresenter` that you can use to protect your resources. It's `checkRequirements()` method validates the access token and fires an `onAuthorized` event with the modified `Psr\Http\Message\ServerRequestInterface` object. The following attributes will be set on it in case of successful validation:

- `oauth_access_token_id` - the access token identifier,
- `oauth_client_id` - the client identifier,
- `oauth_user_id` - the user identifier represented by the access token,
- `oauth_scopes` - an array of string scope identifiers.


Advanced usage
--------------

### Custom approve template

The template of the approve component is [Bootstrap](https://getbootstrap.com) ready, but can be changed using some trait magic:

```php
class ApprovePresenter extends Presenter
{
    use ApprovePresenterTrait {
        createComponentApprove as ___createComponentApprove;
    }
    
    // ...
    
    /**
     * @return ApproveControl
     */
    protected function createComponentApprove()
    {
        $control = $this->___createComponentApprove();
        $control->setTemplateFile(__DIR__ . '/path/to/template.latte');
        return $control;
    }
}
```

The template gets passed a single variable `$authorizationRequest` with a `League\OAuth2\Server\RequestTypes\AuthorizationRequest` object inside containing information about the request being approved.

### Custom grants

Custom grants have to implement `League\OAuth2\Server\Grant\GrantTypeInterface`. Enable them in your `config.neon` like this:

```neon
services:
    - MyCustomGrant
    oauth2.authorizationServer:
        setup:
            - enableGrantType(@MyCustomGrant)
```

### Logging

This package supports standard [PSR-3](http://www.php-fig.org/psr/psr-3) logging. If you have a compliant logger registered as a service, the easiest way to enable it is via `config.neon`:

```neon
decorator:
    Psr\Log\LoggerAwareInterface:
        setup:
            - setLogger
```

### Client secret validation

By default, the `Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository` uses a simple `hash_equals` function to validate the client secret. This means that it expects the secrets in the database to be stored in plaintext, which might not be the best of ideas for obvious reasons. It is therefore **STRONGLY** recommended that you store the secrets hashed (for example with `password_hash()`), and implement your custom secret validator:

```php
class SecretValidator
{
    public function __invoke($expected, $actual)
    {
        return password_verify($actual, $expected);
    }
}
```

Then register it in the config:

```neon
services:
    - SecretValidator
    oauth2.repository.client:
        arguments: [secretValidator: @SecretValidator]
```

### User credentials validation

`Lookyman\NetteOAuth2Server\User\UserRepository` validates user credentials by trying to log the user in. However, if your login process is somehow modified, this can easily fail in unexpected ways. In that case you might need to reimplement the credentials validator. Just get the correct user ID the way your application does it, and return `Lookyman\NetteOAuth2Server\User\UserEntity` (or `null` in case of bad credentials).

```php
class CredentialsValidator
{
    public function __invoke($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        // get the user ID from your application, and
        return new UserEntity($userId);
    }
}
```

Then register it in the config:

```neon
services:
    - CredentialsValidator
    oauth2.repository.user:
        arguments: [credentialsValidator: @CredentialsValidator]
```
