[![SymfonyInsight](https://insight.symfony.com/projects/ac7d49ec-b2aa-452d-817e-c036c7cb8b9f/big.png)](https://insight.symfony.com/projects/ac7d49ec-b2aa-452d-817e-c036c7cb8b9f)

U2F Security Bundle
===================

This Symfony bundle aim to add a two factor security level to your Symfony project.

Overview
--------

If you want to use U2F security keys as second level security, you have 3 options :

1. You are a true warrior and make it all from scratch
2. You use a third party library and create a wrapper around it
3. You use this bundle :-)

This U2F Security Bundle is a wrapper around the [samyoul/u2f-php-server](https://github.com/Samyoul/U2F-php-server) library.

It move all the complexity to the wrapper and all you need is creating entities, making some calls from your controller, and displaying a beautiful form in a beautiful page. That all (for the basics).

Requirements
------------

Before installing this bundle you need to have an already working login and secured area in the Symfony way (aka security bundle, firewall and user entity).

Important point, U2F keys only work on HTTPS requests. So you will need an SSL certificate, even for working on localhost. The good news is that you can use self-signed certificate without problem.

Installation
------------

Globally, the installation process can be splitted into three parts :

1. Composer installation and bundle configuration
2. Creating entities and models
3. Creating controller

Now let's start together !

Installation and configuration
------------------------------

First you need to install the bundle through composer !

`composer require mbarbey/u2f-security-bundle`

Then you need to create the file `config/packages/mbarbey_u2f_security.yml` (because there is no receipe for the moment) and insert the following content :

```yaml
mbarbey_u2f_security:
    authentication_route: user_authenticate_u2f
    whitelist_routes:
        - login
        - logout

```

The key `authentication_route` is required. It's the route where the users will be jailed until they successfully authenticate with their U2F security key. SI tu must be the route where the U2F authentication will be performed.

The key `whitelist_routes` is an optional list on routes where the user can still visit after being logged and and without being authenticated with the two factor security. For example you can whitelist the login and logout routes. These given routes will be added to the following list of already whitelisted routes :

- `_wdt`
- `_profiler_home`
- `_profiler_search`
- `_profiler_search_bar`
- `_profiler_phpinfo`
- `_profiler_search_results`
- `_profiler_open_file`
- `_profiler`
- `_profiler_router`
- `_profiler_exception`
- `_profiler_exception_css`

Great ! It was easy uh ? Now let's create some entities.


Entities and models
-------------------

First, we will create a "key" entity which will store the data of the U2F keys. You have two options :

1. Create a new entity and extends the class `Mbarbey\U2fSecurityBundle\Model\Key\U2fKey`.
2. Create a new entity and implement the interface `Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface`. If you pick this choice, don't forget to set the variables `$keyHandle`, `$publicKey`, `$certificate` and `$counter` public. This is actually needed by the used library. You can look at the `Mbarbey\U2fSecurityBundle\Model\Key\U2fKey` class if you need some inspiration.


Well done, now let's work more with the entities.

You need to edit a little bit your exising `user` entity used by your firewall to be linked to the newly created entity for the U2F keys. Again, you have two options :

1. Extends the class `Mbarbey\U2fSecurityBundle\Model\User\U2fUser`.
2. Implement the interface `Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface` and add the missing functions (`getU2fKeys`, `addU2fKey` and `removeU2fKey`).


Excellent, you have created all the required entities. Now we will need two model which will store the forms data.

You will have to create an empty authentication model which will extends the `Mbarbey\U2fSecurityBundle\Model\U2fAuthentication\U2fAuthentication` class. If you need to, you can add additional data to this model which will store the challenge response generated by the U2F keys, on the authentication page dispayed juste after being logged in.

Next you will have to create an empty registration model which will extends the `Mbarbey\U2fSecurityBundle\Model\U2fRegistration\U2fRegistration` class. Again, if you need to, you can add additional data to this model which will store the identification of the U2F key which will be attached to a user. For example, you can give a name to the keys so the user can recognise them ;-)


Bravo, you have made 80% of the work. Now let's do some easier tasks.


Registration controller
-----------------------

We will first allow users to register security keys.

For this, you will need a controller (new or existing one) and a `registration` action which is only available after being logged in with a username and passowrd (classic).

In this action, you will need to do :

1. Inject the service `Mbarbey\U2fSecurityBundle\Service\U2fSecurity` as argument of your action.
2. Create a form for the registration and use the model you just created in the prevous part. For the form, the field `response` must be hidden.
3. If your form is submitted and valid:
  1. You will need to create a new key with the entity you created too in the previous part and call the function `validateRegistration` from the service you just injected and pass the following arguments :
      - the current user (ex: $this->getUser())
      - the registration data filled by the form
      - the newly created key
  2. If there is an error, this function will throw an exception, so you will need a try catch to handle it and inform the user why his registration failed.
  3. If there is no error, you newly created key has been filled and is ready to be persisted.
4. Else :
  1. You will need store the result of the function `createRegistration` and pass as argument your appId. The appId must always be the HTTP protocol and you domain name. For exemple : https://example.com. For a more dynamic system, you can use the function `getSchemeAndHttpHost` from your HTTP request. Here is an exemple of usage : `$registrationData = $service->createRegistration($request->getSchemeAndHttpHost());`. This data is the registration request which will be sent to the user. It contains two parts : the request and the signatures.
  2. Render a view with :
      - your form
      - the `request` part of your registration request (ex: `$registrationData['request']`)
      - the `signatures` par of your registration request (ex: `$registrationData['signatures']`)

Here is a full exemple for this controller action :

```php
public function u2fRegistration(Request $request, U2fSecurity $service)
{
    $registration = new U2fRegistration();
    $form = $this->createForm(U2fRegistrationType::class, $registration);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $key = new Key();
            $service->validateRegistration($this->getUser(), $registration, $key);

            $em = $this->getDoctrine()->getManager();
            $em->persist($key);
            $em->flush();

            return $this->redirectToRoute('user_keys_list');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
    }

    $registrationData = $service->createRegistration($request->getSchemeAndHttpHost());

    return $this->render('user/key/register.html.twig', array(
        'jsRequest' => $registrationData['request'],
        'jsSignatures' => $registrationData['signatures'],
        'form' => $form->createView(),
    ));
}
```

For the front part, it's up to you. The two things you need are the form, and some JS.
Here is the JS you must use. Feel free to edit it as you want.

```twig
<script src="{{ asset('bundles/mbarbeyu2fsecurity/u2f.js') }}"></script>
<script type="text/javascript">
    setTimeout(function() {
        // A magic JS function that talks to the USB device. This function will keep polling for the USB device until it finds one.
        u2f.register("{{ jsRequest.appId }}", [{version: "{{jsRequest.version}}", challenge: "{{ jsRequest.challenge }}"}], {{ jsSignatures|raw }}, function(data) {
            // Handle returning error data
            if(data.errorCode && data.errorCode != 0) {
                alert("registration failed with error: " + data.errorCode);
                // Or handle the error however you'd like. 
                return;
            }

            // On success process the data from USB device to send to the server
            var registration_response = data;

            // Get the form items so we can send data back to the server
            var form = document.getElementsByTagName('form')[0];
            var response = document.getElementById('{{ form.response.vars.id }}');

            // Fill and submit form.
            response.value = JSON.stringify(registration_response);
            form.submit();
        });
    }, 1000);
</script>
```

And tadaaaa ! You users can register their security keys and link it to their account !

But ! Registering keys is cool, but it will be better to be authenticated with it.

Authentication controller
-------------------------

Now let's to the same thing for the authentication. Keep in mind that this action must match with your authentication route you defined in the configuration of the bundle.

In this second action, you will need to do :

1. Inject the service `Mbarbey\U2fSecurityBundle\Service\U2fSecurity` as argument of your action.
2. Create a form for the authentication and use the model you just created in the prevous part. For the form, the field `response` must be hidden.
3. If your form is submitted and valid:
  1. You need to call the function`validateAuthentication` from the service and give the following arguments :
      - the user (ex: $this->getUser())
      - the authentication data filled by the form
  2. This function will either return the used key from the user and update it counter, or throw an exception so you will need a try catch to handle it and inform the user why his authentication failed.
  3. If there is no error, you can update/save the received key.
4. Else :
  1. You will need to store the result of the function `createAuthentication` and pass as argument your appId and the user to ckeck. Here is an example of usage : `$authenticationRequest = $service->createAuthentication($request->getSchemeAndHttpHost(), $this->getUser());`. This data is the authentication request which will be sent to the user.
  2. Render a view with :
      - your form
      - the authentication request

Here is a full exemple for this controller action :

```php
public function u2fAuthentication(Request $request, U2fSecurity $service)
{
    $authentication = new U2fAuthentication();
    $form = $this->createForm(U2fAuthenticationType::class, $authentication);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $updatedKey = $service->validateAuthentication($this->getUser(), $authentication);

            $em = $this->getDoctrine()->getManager();
            $em->persist($updatedKey);
            $em->flush();

            return $this->redirectToRoute('user_list');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
    }

    $authenticationRequest = $service->createAuthentication($request->getSchemeAndHttpHost(), $this->getUser());

    return $this->render('user/key/authenticate.html.twig', array(
        'authenticationRequest' => $authenticationRequest,
        'form' => $form->createView(),
    ));
}
```

For the front part, it's up to you. The two things you need are the form, and some JS.
Here is the JS you must use. Feel free to edit it as you want.

```twig
<script src="{{ asset('bundles/mbarbeyu2fsecurity/u2f.js') }}"></script>
<script type="text/javascript">
    setTimeout(function() {
        // Magic JavaScript talking to your HID
        u2f.sign("{{ authenticationRequest.appId }}", "{{ authenticationRequest.challenge }}", {{ authenticationRequest.registeredKeys|raw }}, function(data) {

            // Handle returning error data
            if(data.errorCode && data.errorCode != 0) {
                alert("Authentication failed with error: " + data.errorCode);
                // Or handle the error however you'd like. 

                return;
            }

            // On success process the data from USB device to send to the server
            var authentication_response = data;

            // Get the form items so we can send data back to the server
            var form = document.getElementsByTagName('form')[0];
            var response = document.getElementById('{{ form.response.vars.id }}');

            // Fill and submit form.
            response.value = JSON.stringify(authentication_response);
            form.submit();
        });
    }, 1000);
</script>
```

And, congratulation (play success music in the background). Now you users can register some security keys and when they log in, they will be redirected to the authentication page and will be jailed in it until they successfully authenticate with their security key.

Advanced use case
-----------------

Explanation coming soon.