<?php

namespace Yii2Oauth2ServerTests\unit\models;

use rhertogh\Yii2Oauth2Server\interfaces\models\Oauth2ClientInterface;
use rhertogh\Yii2Oauth2Server\models\Oauth2AccessToken;
use rhertogh\Yii2Oauth2Server\models\Oauth2Client;
use rhertogh\Yii2Oauth2Server\Oauth2Module;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;
use Yii2Oauth2ServerTests\unit\models\_base\BaseOauth2ActiveRecordTest;
use Yii2Oauth2ServerTests\unit\models\_traits\Oauth2IdentifierTestTrait;
use Yii2Oauth2ServerTests\unit\models\_traits\Oauth2IdTestTrait;

/**
 * @covers \rhertogh\Yii2Oauth2Server\models\Oauth2Client
 * @covers \rhertogh\Yii2Oauth2Server\models\base\Oauth2Client
 * @covers \rhertogh\Yii2Oauth2Server\models\base\Oauth2BaseActiveRecord
 * @covers \rhertogh\Yii2Oauth2Server\models\queries\Oauth2ClientQuery
 * @covers \rhertogh\Yii2Oauth2Server\models\queries\base\Oauth2BaseActiveQuery
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\BooleanBehavior
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\DateTimeBehavior
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\TimestampBehavior
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\BooleanBehavior
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\DateTimeBehavior
 * @covers \rhertogh\Yii2Oauth2Server\models\behaviors\TimestampBehavior
 *
 * @method Oauth2ClientInterface|ActiveRecord getMockModel(array $config = [])
 */

class Oauth2ClientTest extends BaseOauth2ActiveRecordTest
{
    use Oauth2IdTestTrait;
    use Oauth2IdentifierTestTrait;

    /**
     * @return class-string<Oauth2ClientInterface>
     */
    protected function getModelInterface()
    {
        return Oauth2ClientInterface::class;
    }

    /**
     * @return array[]
     * @see BaseOauth2ActiveRecordTest::testPersist()
     */
    public function persistTestProvider()
    {
        $this->mockConsoleApplication();
        return [
            // Valid.
            [
                [
                    'identifier' => 'my-test-client',
                    'type' => Oauth2ClientInterface::TYPE_CONFIDENTIAL,
                    'name' => 'my test client',
                    'redirect_uris' => 'https://my.test/uri',
                    'token_types' => Oauth2AccessToken::TYPE_BEARER,
                    'grant_types' => Oauth2Module::GRANT_TYPE_AUTH_CODE,
                ],
                true,
                function (Oauth2Client $model) {
                    $model->setSecret('my-test-secret', Oauth2Module::getInstance()->getEncryptor());
                }
            ],
            // Valid, multiple redirect URIs.
            [
                [
                    'identifier' => 'my-test-client',
                    'type' => Oauth2ClientInterface::TYPE_CONFIDENTIAL,
                    'name' => 'my test client',
                    'redirect_uris' => ['https://my.test/uri_1', 'https://my.test/uri_2'],
                    'token_types' => Oauth2AccessToken::TYPE_BEARER,
                    'grant_types' => Oauth2Module::GRANT_TYPE_AUTH_CODE,
                ],
                true,
                function (Oauth2Client $model) {
                    $model->setSecret('my-test-secret', Oauth2Module::getInstance()->getEncryptor());
                }
            ],
            // Invalid (missing secret for type confidential).
            [
                [
                    'identifier' => 'my-test-client',
                    'type' => Oauth2ClientInterface::TYPE_CONFIDENTIAL,
                    'name' => 'my test client',
                    'redirect_uris' => 'https://my.test/uri',
                    'token_types' => Oauth2AccessToken::TYPE_BEARER,
                    'grant_types' => Oauth2Module::GRANT_TYPE_AUTH_CODE,
                ],
                false,
            ],
        ];
    }

    /**
     * @return int[][]
     * @see Oauth2IdTestTrait::testFindById()
     */
    public function findByIdTestProvider()
    {
        return [[1003000]];
    }

    /**
     * @return string[][]
     * @see Oauth2IdentifierTestTrait::testFindByIdentifier()
     */
    public function findByIdentifierTestProvider()
    {
        return [['test-client-type-auth-code-valid']];
    }

    /**
     * @return array[]
     * @see Oauth2IdentifierTestTrait::testIdentifierExists()
     */
    public function identifierExistsProvider()
    {
        return [
            ['test-client-type-auth-code-valid', true],
            ['does-not-exists',                  false],
        ];
    }

    public function testIsEnabled()
    {
        $enabledClient = $this->getMockModel(); // enabled by default.
        $disabledClient = $this->getMockModel(['enabled' => 0]);

        $this->assertEquals(true, $enabledClient->isEnabled());
        $this->assertEquals(false, $disabledClient->isEnabled());
    }

    public function testPropertyGettersSetters()
    {
        $name = 'my-test-client';
        $userAccountSelection = 1;
        $allowAuthCodeWithoutPkce = 2;
        $skipAuthorizationIfScopeIsAllowed = 3;
        $getScopeAccess = 4;
        $clientCredentialsGrantUserId = 5;
        $openIdConnectAllowOfflineAccessWithoutConsent = 6;
        $openIdConnectUserinfoEncryptedResponseAlg = 'RS256';

        $client = $this->getMockModel()
            ->setName($name)
            ->setUserAccountSelection($userAccountSelection)
            ->setAllowAuthCodeWithoutPkce($allowAuthCodeWithoutPkce)
            ->setSkipAuthorizationIfScopeIsAllowed($skipAuthorizationIfScopeIsAllowed)
            ->setScopeAccess($getScopeAccess)
            ->setClientCredentialsGrantUserId($clientCredentialsGrantUserId)
            ->setOpenIdConnectAllowOfflineAccessWithoutConsent($openIdConnectAllowOfflineAccessWithoutConsent)
            ->setOpenIdConnectUserinfoEncryptedResponseAlg($openIdConnectUserinfoEncryptedResponseAlg);

        // phpcs:disable Generic.Files.LineLength.TooLong -- readability actually better on single line
        $this->assertEquals($name, $client->getName());
        $this->assertEquals($userAccountSelection, $client->getUserAccountSelection());
        $this->assertEquals($allowAuthCodeWithoutPkce, $client->isAuthCodeWithoutPkceAllowed());
        $this->assertEquals($skipAuthorizationIfScopeIsAllowed, $client->skipAuthorizationIfScopeIsAllowed());
        $this->assertEquals($getScopeAccess, $client->getScopeAccess());
        $this->assertEquals($clientCredentialsGrantUserId, $client->getClientCredentialsGrantUserId());
        $this->assertEquals($openIdConnectAllowOfflineAccessWithoutConsent, $client->getOpenIdConnectAllowOfflineAccessWithoutConsent());
        $this->assertEquals($openIdConnectUserinfoEncryptedResponseAlg, $client->getOpenIdConnectUserinfoEncryptedResponseAlg());
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    public function testGetRedirectUri()
    {
        $redirectUris = [
            'https://localhost/redirect_uri_1',
            'https://localhost/redirect_uri_2',
        ];
        $client = $this->getMockModel(['redirect_uris' => Json::encode($redirectUris)]);

        $this->assertEquals($redirectUris, $client->getRedirectUri());
    }

    public function testGetRedirectUriInvalidJson()
    {
        $client = $this->getMockModel([
            'id' => 1,
            'redirect_uris' => '[https://localhost/redirect_uri_1',
        ]);

        $this->expectExceptionMessage('Invalid json in redirect_uris for client 1');
        $client->getRedirectUri();
    }

    public function testSetRedirectUriString()
    {
        $client = $this->getMockModel();
        $redirectUri = 'https://localhost/redirect_uri_1';
        $client->setRedirectUri($redirectUri);
        $this->assertEquals(Json::encode([$redirectUri]), $client->redirect_uris);
    }

    public function testSetRedirectUriArray()
    {
        $client = $this->getMockModel();
        $redirectUris = [
            'https://localhost/redirect_uri_1',
            'https://localhost/redirect_uri_2',
        ];

        $client->setRedirectUri($redirectUris);
        $this->assertEquals(Json::encode($redirectUris), $client->redirect_uris);
    }

    public function testSetRedirectUriInvalidType()
    {
        $client = $this->getMockModel();
        $this->expectExceptionMessage('$uri must be a string or an array, got: object');
        $client->setRedirectUri(new \stdClass());
    }

    public function testSetRedirectUriInvalidArray()
    {
        $client = $this->getMockModel();
        $redirectUris = [
            'https://localhost/redirect_uri_1',
            new \stdClass(),
        ];

        $this->expectExceptionMessage('When $uri is an array, it\'s values must be strings.');
        $client->setRedirectUri($redirectUris);
    }

    public function testIsConfidential()
    {
        $confidentialClient = $this->getMockModel(['type' => Oauth2ClientInterface::TYPE_CONFIDENTIAL]);
        $publicClient = $this->getMockModel(['type' => Oauth2ClientInterface::TYPE_PUBLIC]);

        $this->assertEquals(true, $confidentialClient->isConfidential());
        $this->assertEquals(false, $publicClient->isConfidential());
    }

    public function testRotateStorageEncryptionKeys()
    {
        $oldKeyName = '2021-01-01';
        $newKeyName = '2022-01-01'; // default key name.
        $encryptor = Oauth2Module::getInstance()->getEncryptor();

        Oauth2Client::updateAll(['old_secret' => new Expression('secret')], ['NOT', ['secret' => null]]);

        $clients = Oauth2Client::find()->andWhere(['NOT', ['secret' => null]])->all();

        foreach ($clients as $client) {
            $ciphertext = $client->getAttribute('secret');
            $this->assertStringStartsWith($oldKeyName . '::', $ciphertext);

            $ciphertext = $client->getAttribute('old_secret');
            $this->assertStringStartsWith($oldKeyName . '::', $ciphertext);
        }

        Oauth2Client::rotateStorageEncryptionKeys($encryptor);

        foreach ($clients as $client) {
            $client->refresh();

            $ciphertext = $client->getAttribute('secret');
            $this->assertStringStartsWith($newKeyName . '::', $ciphertext);

            $ciphertext = $client->getAttribute('old_secret');
            $this->assertStringStartsWith($newKeyName . '::', $ciphertext);
        }
    }

    public function testRotateStorageEncryptionKeysFailure()
    {
        /** @var class-string<Oauth2Client> $modelClass */
        $modelClass = get_class(new class extends Oauth2Client {
            public static function tableName()
            {
                return 'oauth2_client';
            }

            public function persist($runValidation = true, $attributeNames = null)
            {
                throw new \Exception('test');
            }
        });

        $encryptor = Oauth2Module::getInstance()->getEncryptor();

        $this->expectException(\Exception::class);
        $modelClass::rotateStorageEncryptionKeys($encryptor);
    }

    public function testRotateStorageEncryptionKey()
    {
        $secret = 'my-test-secret';
        $oldKeyName = '2021-01-01';
        $newKeyName = '2022-01-01'; // default key name.
        $encryptor = Oauth2Module::getInstance()->getEncryptor();
        $client = $this->getMockModel();
        $client->setSecret($secret, $encryptor, null, $oldKeyName);

        $ciphertext = $client->getAttribute('secret');
        $this->assertStringStartsWith($oldKeyName . '::', $ciphertext);

        $client->rotateStorageEncryptionKey($encryptor);
        $this->assertStringStartsWith($newKeyName . '::', $client->getAttribute('secret'));

        $this->assertEquals($secret, $client->getDecryptedSecret($encryptor));
    }

    public function testSecret()
    {
        $oldKeyName = '2021-01-01';
        $newKeyName = '2022-01-01'; // default key name.

        $secret = 'my-test-secret';
        $secret2 = 'my-test-secret-2';
        $secret3 = 'my-test-secret-3';

        $encryptor = Oauth2Module::getInstance()->getEncryptor();

        $client = $this->getMockModel();
        $client->setSecret($secret, $encryptor);

        $ciphertext = $client->getAttribute('secret');
        $this->assertStringStartsWith($newKeyName . '::', $ciphertext);
        $this->assertFalse(strpos($secret, $client->getAttribute('secret')));
        $this->assertEquals($secret, $client->getDecryptedSecret($encryptor));
        $this->assertTrue($client->validateSecret($secret, $encryptor));
        $this->assertFalse($client->validateSecret('incorrect', $encryptor));

        $client->setSecret($secret2, $encryptor, new \DateInterval('P1D'), $oldKeyName);
        $ciphertext = $client->getAttribute('secret');
        $this->assertStringStartsWith($oldKeyName . '::', $ciphertext);
        $ciphertext = $client->getAttribute('old_secret');
        $this->assertStringStartsWith($oldKeyName . '::', $ciphertext);
        $this->assertEquals($secret2, $client->getDecryptedSecret($encryptor));
        $this->assertTrue($client->validateSecret($secret2, $encryptor)); // new secret.
        $this->assertTrue($client->validateSecret($secret, $encryptor)); // old secret (which should still be valid).
        $this->assertFalse($client->validateSecret('incorrect', $encryptor));

        $client->setSecret($secret3, $encryptor, (new \DateTimeImmutable('yesterday')));
        $ciphertext = $client->getAttribute('secret');
        $this->assertStringStartsWith($newKeyName . '::', $ciphertext);
        $ciphertext = $client->getAttribute('old_secret');
        $this->assertStringStartsWith($newKeyName . '::', $ciphertext);
        $this->assertEquals($secret3, $client->getDecryptedSecret($encryptor));
        $this->assertTrue($client->validateSecret($secret3, $encryptor)); // new secret.
        $this->assertFalse($client->validateSecret($secret2, $encryptor)); // old secret (which has expired).
        $this->assertFalse($client->validateSecret('incorrect', $encryptor));
    }

    /**
     * @depends testSecret
     */
    public function testSecretLength()
    {
        $client = $this->getMockModel();

        $this->expectException(InvalidArgumentException::class);
        $client->setSecret('too-short', Oauth2Module::getInstance()->getEncryptor());
    }

    public function testSetSecretViaProperty()
    {
        $this->expectExceptionMessage('For security the "secret" property must be set via setSecret()');
        $this->getMockModel([
            'secret' => 'test',
        ]);
    }

    /**
     * @depends testSecret
     */
    public function testSetSecretForTypeNonConfidential()
    {
        $encryptor = Oauth2Module::getInstance()->getEncryptor();
        $client = $this->getMockModel([
            'type' => Oauth2ClientInterface::TYPE_CONFIDENTIAL,
        ]);

        $client->setSecret('my-test-secret', $encryptor);

        $this->assertNotEmpty($client->secret);
        $client->type = Oauth2ClientInterface::TYPE_PUBLIC;
        $client->setSecret(null, $encryptor); // Setting secret to `null` on public type should be allowed.
        $this->assertNull($client->secret);

        $this->expectExceptionMessage('The secret for a non-confidential client can only be set to `null`');
        $client->setSecret('my-test-secret', $encryptor);
    }

    public function testValidateGrantType()
    {
        $client = $this->getMockModel();
        $client->setGrantTypes(
            Oauth2Module::GRANT_TYPE_AUTH_CODE | Oauth2Module::GRANT_TYPE_CLIENT_CREDENTIALS
        );

        $this->assertEquals(true, $client->validateGrantType('authorization_code'));
        $this->assertEquals(true, $client->validateGrantType('client_credentials'));
        $this->assertEquals(false, $client->validateGrantType('refresh_token'));
        $this->assertEquals(false, $client->validateGrantType('password'));
    }

    /**
     * @depends testValidateGrantType
     */
    public function testValidateGrantTypeWithUnknownType()
    {
        $client = $this->getMockModel([
            'grant_types' => Oauth2Module::GRANT_TYPE_AUTH_CODE | Oauth2Module::GRANT_TYPE_CLIENT_CREDENTIALS
        ]);

        $this->expectExceptionMessage('Unknown grant type "does-not-exist"');
        $client->validateGrantType('does-not-exist');
    }

    public function testSetInvalidGrantTypes()
    {
        $this->expectExceptionMessage('Unknown Grant Type ID: 8192');
        $this->getMockModel()->setGrantTypes(
            9999
        );
    }

    /**
     * @dataProvider validateAuthRequestScopesProvider
     */
    public function testValidateAuthRequestScopes(
        $requestedScopeIdentifiers,
        $scopeAccess,
        $expected,
        $expectedUnauthorizedScopes
    ) {
        $client = $this->getMockModel([
            'id' => 1003000,
            'scope_access' => $scopeAccess,
        ]);

        $this->assertEquals(
            $expected,
            $client->validateAuthRequestScopes($requestedScopeIdentifiers, $unauthorizedScopes)
        );
        $this->assertEquals($expectedUnauthorizedScopes, $unauthorizedScopes);
    }

    public function validateAuthRequestScopesProvider()
    {
        // phpcs:disable Generic.Files.LineLength.TooLong -- readability acually better on single line
        return [
            [['user.username.read', 'user.email_address.read'], Oauth2Client::SCOPE_ACCESS_STRICT, true, []],
            [['user.username.read', 'user.email_address.read'], Oauth2Client::SCOPE_ACCESS_STRICT_QUIET, true, []],
            [['user.username.read', 'disabled-scope'], Oauth2Client::SCOPE_ACCESS_STRICT, false, ['disabled-scope']],
            [['user.email_address.read', 'disabled-scope-for-client'], Oauth2Client::SCOPE_ACCESS_STRICT, false, ['disabled-scope-for-client']],
            [['defined-but-not-assigned'], Oauth2Client::SCOPE_ACCESS_STRICT, false, ['defined-but-not-assigned']],
            [['non-existing'], Oauth2Client::SCOPE_ACCESS_STRICT, false, ['non-existing']],

            [['defined-but-not-assigned'], Oauth2Client::SCOPE_ACCESS_STRICT_QUIET, true, []],
            [['non-existing'], Oauth2Client::SCOPE_ACCESS_STRICT_QUIET, true, []],

            [['defined-but-not-assigned'], Oauth2Client::SCOPE_ACCESS_PERMISSIVE, true, []],
            [['non-existing'], Oauth2Client::SCOPE_ACCESS_PERMISSIVE, false, ['non-existing']],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    /**
     * @dataProvider getAllowedScopesProvider
     */
    public function testGetAllowedScopes($clientId, $requestedScopeIdentifiers, $scopeAccess, $expectedScopes)
    {
        $client = $this->getMockModel([
            'id' => $clientId,
            'scope_access' => $scopeAccess,
        ]);

        $allowedScopeIdentifiers = array_column($client->getAllowedScopes($requestedScopeIdentifiers), 'identifier');
        $this->assertEquals($expectedScopes, $allowedScopeIdentifiers);
    }

    public function getAllowedScopesProvider()
    {
        return [
            [
                1003000,
                [],
                Oauth2Client::SCOPE_ACCESS_STRICT,
                [
                    'user.id.read',
                    'applied-by-default',
                    'applied-by-default-for-client',
                    'applied-automatically-by-default',
                    'applied-automatically-by-default-for-client',
                    'applied-by-default-by-client-not-required-for-client',
                ],
            ],
            [
                1003000,
                [],
                Oauth2Client::SCOPE_ACCESS_PERMISSIVE,
                [
                    'user.id.read',
                    'applied-by-default',
                    'applied-by-default-for-client',
                    'applied-automatically-by-default',
                    'applied-automatically-by-default-for-client',
                    'applied-automatically-by-default-not-assigned',
                    'applied-automatically-by-default-not-assigned-not-required',
                    'applied-by-default-by-client-not-required-for-client',
                ],
            ],
            [
                1003000,
                [
                    'user.username.read',
                    'user.email_address.read',
                    'defined-but-not-assigned',
                    'disabled-scope',
                    'disabled-scope-for-client',
                    'non-existing',
                ],
                Oauth2Client::SCOPE_ACCESS_STRICT,
                [
                    'user.id.read',
                    'user.username.read',
                    'user.email_address.read',
                    'applied-by-default',
                    'applied-by-default-for-client',
                    'applied-automatically-by-default',
                    'applied-automatically-by-default-for-client',
                    'applied-by-default-by-client-not-required-for-client',
                ],
            ],
            [
                1003000,
                [
                    'user.username.read',
                    'user.email_address.read',
                    'defined-but-not-assigned',
                    'disabled-scope',
                    'disabled-scope-for-client',
                    'non-existing',
                ],
                Oauth2Client::SCOPE_ACCESS_PERMISSIVE,
                [
                    'user.id.read',
                    'user.username.read',
                    'user.email_address.read',
                    'defined-but-not-assigned',
                    'applied-by-default',
                    'applied-by-default-for-client',
                    'applied-automatically-by-default',
                    'applied-automatically-by-default-for-client',
                    'applied-automatically-by-default-not-assigned',
                    'applied-automatically-by-default-not-assigned-not-required',
                    'applied-by-default-by-client-not-required-for-client',
                ],
            ],
        ];
    }

    public function testValidateAuthRequestScopesInvalid()
    {
        $client = $this->getMockModel([
            'id' => 1003000,
            'scope_access' => 999999999,
        ]);

        $this->expectExceptionMessage('Unknown scope_access: "999999999".');
        $client->validateAuthRequestScopes(['user.username.read']);
    }
}
