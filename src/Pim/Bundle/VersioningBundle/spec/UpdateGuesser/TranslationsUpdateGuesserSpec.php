<?php

namespace spec\Pim\Bundle\VersioningBundle\UpdateGuesser;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Entity\Category;
use Pim\Bundle\CatalogBundle\Entity\CategoryTranslation;
use Pim\Bundle\VersioningBundle\UpdateGuesser\TranslationsUpdateGuesser;
use Pim\Bundle\VersioningBundle\UpdateGuesser\UpdateGuesserInterface;
use Pim\Component\Catalog\Model\LocaleInterface;

class TranslationsUpdateGuesserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['stdClass']);
    }

    function it_is_an_update_guesser()
    {
        $this->shouldImplement(UpdateGuesserInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TranslationsUpdateGuesser::class);
    }

    function it_supports_update_action()
    {
        $this->supportAction(UpdateGuesserInterface::ACTION_UPDATE_ENTITY)->shouldReturn(true);
        $this->supportAction('foo')->shouldReturn(false);
    }

    function it_supports_delete_action()
    {
        $this->supportAction(UpdateGuesserInterface::ACTION_DELETE)->shouldReturn(true);
        $this->supportAction('bar')->shouldReturn(false);
    }

    function it_guesses_translatable_entity_updates(
        Category $category,
        CategoryTranslation $translation,
        EntityManager $em
    ) {
        $translation->getForeignKey()->willReturn($category);
        $this->guessUpdates($em, $translation, UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
            ->shouldReturn([$category]);

        $translation->getForeignKey()->willReturn($class = new \stdClass());
        $this->guessUpdates($em, $translation, UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
            ->shouldReturn([$class]);
    }

    function it_returns_no_pending_updates_if_not_given_abstract_translation(EntityManager $em)
    {
        $this->guessUpdates($em, new \stdClass(), UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
            ->shouldReturn([]);
    }

    function it_returns_no_pending_updates_if_not_given_versionable_class(
        CategoryTranslation $translation,
        EntityManager $em,
        LocaleInterface $locale
    ) {
        $translation->getForeignKey()->willReturn($locale);
        $this->guessUpdates($em, $translation, UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
            ->shouldReturn([]);
    }
}
