<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;
use MessagePack\Packer;
use MessagePack\Unpacker;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('RobyulWebBundle:Default:index.html.twig');
    }

    /**
     * @Route("/commands")
     */
    public function commandsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Commands - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View a list of all Robyul Discord Bot commands here.")
            ->addMeta('property', 'og:description', "View a list of all Robyul Discord Bot commands here.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:commands.html.twig');
    }

    /**
     * @Route("/statistics")
     */
    public function statisticsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Statistics - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View statistics for Robyul.")
            ->addMeta('property', 'og:description', "View statistics for Robyul.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:statistics.html.twig');
    }

    /**
     * @Route("/profile/backgrounds")
     */
    public function profileBackgroundsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Profile Backgrounds - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Customize your Robyul Discord Bot Profile using Background Pictures.")
            ->addMeta('property', 'og:description', "Customize your Robyul Discord Bot Profile using Background Pictures.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $unpacker = new Unpacker();
        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:db:backgrounds';
        if ($redis->exists($key) == true) {
            $backgrounds = unserialize($unpacker->unpack($redis->get($key)));
        } else {
            $conn = \r\connect('localhost', 28015, $this->container->getParameter('rethinkdb_database'));

            $backgroundsIterator = \r\table("profile_backgrounds")->run($conn);

            $backgrounds = array();
            foreach ($backgroundsIterator as $backgroundIterator) {
                $backgrounds[$backgroundIterator["id"]] = iterator_to_array($backgroundIterator);
            }

            $redis->set($key, $packer->pack(serialize($backgrounds)));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        $tags = array();
        foreach ($backgrounds as $background) {
            if (array_key_exists('tags', $background)) {
                foreach ($background['tags'] as $tag) {
                    if (!in_array($tag, $tags)) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        return $this->render('RobyulWebBundle:Default:profileBackgrounds.html.twig', array(
            'backgrounds' => $backgrounds,
            'tags' => $tags
        ));
    }

    /**
     * @Route("/guides/bias-roles")
     */
    public function biasRoleGuidesAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Bias Roles - Guides - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Learn how to set up Bias Role self assignment for your users.")
            ->addMeta('property', 'og:description', "Learn how to set up Bias Role self assignment for your users.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:guides/biasroles.html.twig');
    }

    /**
     * @Route("/imprint")
     */
    public function imprintAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Impressum - The KPop Discord Bot - Robyul");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:imprint.html.twig');
    }

    /**
     * @Route("/privacy-policy")
     */
    public function privacyPolicyAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Datenschutzerklärung - The KPop Discord Bot - Robyul");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:privacyPolicy.html.twig');
    }

    /**
     * @Route("/ranking/{guildID}",
     *     defaults={"guildID": "global"}
     * )
     */
    public function rankingAction($guildID)
    {
        $unpacker = new Unpacker();
        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');

        $guildName = 'Global';
        $metaName = 'View the Global Robyul Ranking.';
        $guildIcon = '';

        $key = 'robyul2-web:api:guild:' . $guildID;
        if ($guildID !== 'global') {
            if ($redis->exists($key) == true) {
                $guildData = unserialize($unpacker->unpack($redis->get($key)));
            } else {
                $guildInfo = Unirest\Request::get('http://localhost:2021/guild/' . $guildID);
                $guildData = (array)$guildInfo->body;

                $redis->set($key, $packer->pack(serialize($guildData)));
                $redis->expireat($key, strtotime("+1 hour"));
            }
            $guildName = $guildData['Name'];
            $guildIcon = $guildData['Icon'];
            $metaName = 'View the Robyul Ranking for ' . $guildName . '.';
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Ranking - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', $metaName)
            ->addMeta('property', 'og:description', $metaName);
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());


        $key = 'robyul2-web:api:rankings:' . $guildID;
        if ($redis->exists($key) == true) {
            $rankingData = unserialize($unpacker->unpack($redis->get($key)));
        } else {
            $rankingInfo = Unirest\Request::get('http://localhost:2021/rankings/' . $guildID);
            $rankingData = (array)$rankingInfo->body;

            $redis->set($key, $packer->pack(serialize($rankingData)));
            $redis->expireat($key, strtotime("+1 hour"));
        }

        return $this->render('RobyulWebBundle:Default:ranking.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'rankings' => $rankingData
        ));
    }
}
