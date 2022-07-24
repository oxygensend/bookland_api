<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AuthorizationTest extends ApiTestCase
{
    use RefreshDatabaseTrait;


    public function testUserRegistration(): void
    {

        $this->createRegistrationRequest();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@type' => 'User',
            'email' => 'test@test.com',
            'name' => 'Test'
        ]);
    }

//    public function testUserRegistrationBlankJsonBody(): void
//    {
//        static::createClient()->request('POST','/api/register', [ 'json' => []]);
//
//        $this->assertResponseStatusCodeSame(400);
//        $this->assertJsonContains(['x']);
//
//
//    }


    public function testUserRegistrationValidationEmail(): void
    {
        $this->createRegistrationRequest(email: 'test.com');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: This value is not a valid email address.'
        ]);
    }

    public function testUserRegistrationValidationName(): void
    {
        $this->createRegistrationRequest(name: 'a');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: Name have to be at least 2 characters'
        ]);

        $this->createRegistrationRequest(name: str_repeat('a', 55));
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: Name have to be no longer than 50 characters'
        ]);

    }

    public function testUserRegistrationValidationSurname(): void
    {
        $this->createRegistrationRequest(surname: 'a');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'surname: Surname have to be at least 2 characters'
        ]);

        $this->createRegistrationRequest(surname: str_repeat('a', 55));
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'surname: Surname have to be no longer than 50 characters'
        ]);

    }

    public function testUserRegistrationValidationPassword(): void
    {
        $this->createRegistrationRequest(password: 'Password', passswordConfirmation: 'Password');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'password: Password have to be minimum 8 characters and contains at least one letter and number.'
        ]);

    }

    public function testUserRegistrationValidationPasswordAndConfirmationPassword(): void
    {
        $this->createRegistrationRequest(password: 'Password1', passswordConfirmation: 'Password');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'The password and confirmation fields are not equal.'
        ]);

    }

    public function testIfConfirmationEmailWasSend()
    {
        $this->createRegistrationRequest();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailTextBodyContains($email, 'Click link below and finish registration process');

    }

//    public function testEmailVerification()
//    {
//        $client = $this->createRegistrationRequest();
//        $client->disableReboot();
//
//        $this->assertResponseStatusCodeSame(201);
//        $email = $this->getMailerMessage();
//        $url = trim(explode('\n', $email->toString())[1]);
//        $query = parse_url($url)['query'];
//
//        $response = $client->request('GET', '/api/verify_email/3?'.$query);
//
//        $this->assertJsonContains(['success' => 'Your e-mail address has been verified.']);
//    }

    public function testResendEmailVerificationToken()
    {

        static::createClient()->request('POST', '/api/resend_token/1', ['json' => []]);

        $this->assertJsonContains(['success' => 'Verification link has been sent. Please check your email']);
        $this->assertResponseStatusCodeSame(200);

        // Check if email is delivered
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailTextBodyContains($email, 'Click link below and finish registration process');

    }

//    public function testResendEmailVerificatonTokenWhenEmailIsVerificated()
//    {
//
//
//        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['id' => 1]);
//        print_r($user);
//        static::createClient()->request('POST', '/api/resend_token/1', ['json' => []]);
//
//        $this->assertJsonContains(['success' => 'Verification link has been sent. Please check your email']);
//        $this->assertResponseStatusCodeSame(200);
//
//        // Check if email is delivered
//        $this->assertEmailCount(1);
//        $email = $this->getMailerMessage();
//        $this->assertEmailTextBodyContains($email, 'Click link below and finish registration process');
//    }

    public function testGetUserDataAuthenticatedAunonymously()
    {
        $url = $this->findIriBy(User::class, ['id' => 1]);
        static::createClient()->request('GET', $url);

        $this->assertResponseStatusCodeSame(401);

    }

    public function testGetAuthenticationToken()
    {
        $user = $this->createUser();
        static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'test@test.com',
            'password' => 'PasswordTest123'
        ]]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetAuthenticationTokenWithNotConfirmedEmail()
    {
        $client = $this->createRegistrationRequest();
        $client->disableReboot();
        $client->request('POST', '/authentication_token', ['json' => [
            'email' => 'test@test.com',
            'password' => 'PasswordTest123'
        ]]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'Your address email is not confirmed.']);

    }

    private function createRegistrationRequest($email = 'test@test.com',
                                               $name = 'Test',
                                               $surname = 'Test',
                                               $password = 'PasswordTest123',
                                               $passswordConfirmation = 'PasswordTest123'): Client
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', ['json' => [
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'passwordConfirmation' => $passswordConfirmation
        ]]);

        return $client;

    }

    private function createUser($email = 'test@test.com',
                                $name = 'Test',
                                $surname = 'Test',
                                $password = 'PasswordTest123'): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmailConfirmedAt(new \DateTime());

        $encoded = self::getContainer()->get('security.user_password_hasher')
            ->hashPassword($user, $password);
        $user->setPassword($encoded);

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

}
