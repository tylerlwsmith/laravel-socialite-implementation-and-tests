<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as OAuth2User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('authentication routes', function () {
    it('redirects login route to correct Google URL', function () {
        $response = get('/auth/google/redirect');

        $redirect_url = $response->getTargetUrl();
        $parsed_query = []; // passed by reference to parse_str()
        parse_str(parse_url($redirect_url)['query'] ?? '', $parsed_query);

        $response->assertStatus(302);
        expect($redirect_url)->toStartWith(
            'https://accounts.google.com/o/oauth2/auth'
        );
        expect($parsed_query)->toHaveKeys([
            'client_id',
            'redirect_uri',
            'scope',
            'response_type',
            'state'
        ]);
    });

    it('creates and authenticates a user that does not yet exist', function () {
        $oauth_user = new OAuth2User();
        $oauth_user->id = '12345';
        $oauth_user->name = 'Tyler Smith';
        $oauth_user->email = 'tyler.smith@example.com';
        $oauth_user->token = '123456789abcdef';
        $oauth_user->refreshToken = '123456789abcdef';

        $mock_provider = Mockery::mock(SocialiteProvider::class);
        $mock_provider->shouldReceive('user')->andReturn($oauth_user);
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mock_provider);

        get('/auth/google/callback');

        assertAuthenticated();
        expect(User::count())->toBe(1);
        expect(Auth::user()->email)->toBe($oauth_user->email);
    });

    it('authenticates an existing user', function () {
        $oauth_user = new OAuth2User();
        $oauth_user->id = '12345';
        $oauth_user->name = 'Tyler Smith';
        $oauth_user->email = 'tyler.smith@example.com';
        $oauth_user->token = '123456789abcdef';
        $oauth_user->refreshToken = '123456789abcdef';

        $app_user = User::factory()->create([
            'email' => $oauth_user->email,
        ]);

        $mock_provider = Mockery::mock(SocialiteProvider::class);
        $mock_provider->shouldReceive('user')->andReturn($oauth_user);
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mock_provider);

        get('/auth/google/callback');

        assertAuthenticated();
        expect(User::count())->toBe(1);
        expect(Auth::id())->toBe($app_user->id);
    });

    it('redirects authenticated user to the homepage', function () {
        $oauth_user = new OAuth2User();
        $oauth_user->id = '12345';
        $oauth_user->name = 'Tyler Smith';
        $oauth_user->email = 'tyler.smith@example.com';
        $oauth_user->token = '123456789abcdef';
        $oauth_user->refreshToken = '123456789abcdef';

        $mock_provider = Mockery::mock(SocialiteProvider::class);
        $mock_provider->shouldReceive('user')->andReturn($oauth_user);
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mock_provider);

        $response = get('/auth/google/callback');

        assertAuthenticated();
        $response->assertRedirect('/');
    });

    it('returns 400 when the oauth callback url is requested directly', function () {
        $response = get('/auth/google/callback');

        $response->assertStatus(400);
        assertGuest();
    });
});
