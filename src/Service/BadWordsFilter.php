<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BadWordsFilter
{
    private array $badWords;
    private string $filePath;
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        // Définition correcte de filePath
        $this->filePath = $this->parameterBag->get('kernel.project_dir') . '/config/bad_words.txt';

        // Vérifier l'existence du fichier avant de l'ouvrir
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException('Le fichier bad_words.txt est manquant dans le dossier config.');
        }

        // Charger les mots interdits depuis le fichier
        $this->badWords = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Vérifier si le fichier est vide ou mal formaté
        if (empty($this->badWords)) {
            throw new \RuntimeException('Le fichier bad_words.txt est vide ou mal formaté.');
        }
    }

    /**
     * Filtre les mots interdits dans une chaîne de caractères.
     *
     * @param string $text Le texte à filtrer
     * @return string Le texte filtré
     */
    public function filter(string $text): string
    {
        foreach ($this->badWords as $badWord) {
            $replacement = str_repeat('#', strlen($badWord)); // Remplace par des #
            $text = str_ireplace($badWord, $replacement, $text); // Ignore la casse
        }
        return $text;
    }

}