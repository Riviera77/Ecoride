<?php

namespace App\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterFormTest extends WebTestCase
{
    public function testCannotRegisterWithDuplicateEmail(): void
    {
        $client = static::createClient();

        // 1. Visite la page d'inscription
        $crawler = $client->request('GET', '/register');

        // CI : vérifier que la page a bien été servie avant même d’interagir avec le formulaire.
        $this->assertResponseIsSuccessful(); // Code 200 attendu

        // 2. Remplit le formulaire avec un email déjà utilisé (présent via tes fixtures)
        $form = $crawler->selectButton("S'inscrire")->form([
            'register_form[username]' => 'GreyTest1',
            'register_form[email]' => 'vanessa13@example.com',
            'register_form[plainPassword][first]' => 'TestPassword123',
            'register_form[plainPassword][second]' => 'TestPassword123',
        ]);

        // 3. Soumet le formulaire
        $client->submit($form);

        // CI : to see what symfony returns in GitHub Actions
        echo $client->getResponse()->getContent();

        // 4. On vérifie que l’utilisateur N’EST PAS redirigé
        $this->assertResponseStatusCodeSame(200);

        // 5. Affiche le HTML en cas de débug
        // echo $client->getResponse()->getContent();

        // 6. Vérifie qu’une erreur est bien affichée (peu importe où)
        $this->assertStringContainsString('déjà utilisé', $client->getResponse()->getContent());
    }
}