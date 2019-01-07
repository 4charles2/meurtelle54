<?php

namespace CHARLY\PlatformBundle\Repository;

use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Va récupérer toutes les entités advert en base de données avec
     * ces jointures
     *
     * @return array toutes les adverts en base de donnée
     */
    public function getAdverts()
    {
        $qb = $this->createQueryBuilder('a')
            //Jointure sur l'attribut image
            ->leftJoin('a.image', 'i')
            ->addSelect('i')
            //Jointure sur l'attribut categories
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->orderBy('a.date', 'DESC')
            ;

        return $qb->getQuery()->getResult();
    }

    public function getAdvert($id)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
        ;
        //Ajoute les tables en jointure
        $this->addJoin($qb, ['a.image', 'a.categories']);

        //Selectionne uniquement les entites correspondante
        // avec l'advert id en parametre
        $qb->andWhere('i.advert_id = :id')
            ->setParameter('id', 'a.id')
            ->andWhere('c.advert_id =:id')
            ->setParameter('id', 'a.id')
            ;
        //todo effacer le commentaire ci-dessous si fonctionne
            /*
            ->leftJoin('a.image', 'i')
            ->addSelect('i')
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ;
            */

            return $qb->getQuery()->getResult();
    }

    /**
     *  Ajoute les tables en jointure avec leftJoin
     *  et les selectionnes addSelect
     *
     * @param QueryBuilder $qb
     * @param              $names
     */
    public function addJoin(QueryBuilder $qb, $names){

        if(is_array($names)){
            foreach ($names as $name) {
                $alias = preg_replace('/\.(.).*/', '$1', $name);
                $qb->leftJoin($name, $alias)
                    ->addSelect($alias);
            }
        }
        else{
            $alias = preg_replace('/\.(.).*/', '$1', $names);
            $qb->leftJoin($names, $alias)
                ->addSelect($alias);
        }
    }
    //Jointure pour telecharger les images en même temps que les annonces
    /**
     * retun l'entite avec toute les annonces en incluant également les images
     *
     * @return Entity Advert with IMAGE
     */

    public function getAdvertsWithImages()
    {
        //todo faire un test juste avec andWhere
        $qb = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.image', 'img')
            ->addSelect('img');

            return $qb->getQuery()
            ->getResult()
            ;
    }
    //Jointure avec le queryBuilder
    public function getAdvertWithApplication()
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.application', 'app')
            ->addSelect('app')
            ;
        

        return $qb
            ->getQuery()
            ->getResult();
    }


    //Recupere toutes les annonces qui corresponde à une catégories

    /**
     * Return list advert tie in $categoryNames
     *
     * @param array $categoryNames
     *
     * @return array
     */

    public function getAdvertWithCategories(array $categoryNames)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->addSelect('c');

            $qb->where($qb->expr()->in('c.name', $categoryNames));

        return $qb->getQuery()->getResult();
    }

    public function myFind()
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->where('a.author = :author')
            ->setParameter('author', 'marine')
            ;
        //J'ajoute ma méthode perso
        $this->whereCurrentyear($qb);

    }

    /**
     * Retourne les annonces de l'auteur de l'année en cours
     *
     * @param QueryBuilder $qb
     *
     * @throws \Exception
     */
    public function whereCurrentyear(QueryBuilder $qb, $author)
    {
        $qb
            ->andWhere('a.date BETWEEN :start AND :end')
            ->setParameter('start', new \Datetime(date('Y').'-01-01'))
            ->setParameter('end', new \DateTime('Y').'-12-31')
            ;
    }

    /**
     * Recherche des annonces ecrit par $author avant $year date
     * Trier par ordre DESC decroissant
     *
     * @param $author
     * @param $year
     *
     * @return array
     */
    public function findByAuthorAndDate($author, $year)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.author = :author')
            ->setParameter('author', $author)
            ->andWhere('a.date < :date')
            ->setParameter('date', $year)
            ->orderBy('a.date', 'DESC');

        return $qb->getQuery()->getResult();
    }
    //Exemple d'utilisation ses requete existe déjà dans la classe EntityRepository
    /*
    public function myFindAll()
    {
        // Méthode 1 : en passant par l'entityManager
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select("a")
            ->from($this->_entityName, 'a');

        //Dans un repository, $this->_entityName est le namespace de l'entité gérée
        //Ici, il vaut donc CHARLYPlatformBundle\Entity\Advert

        //Méthode 2 : en passant par le raccourci (RECOMMANDÉ)
        $queryBuilder = $this->createQueryBuilder('a');

        //On ajoute pas de critère ou tri particulier, la construction de notre requete est finie

        //On récupère la Query à partir du QueryBuilder
        $query = $queryBuilder->getQuery();

        //On récupère les résultats à partir de la Query
        $results = $query->getresult();

        //On retourne ces résultats
        return $results;


        //De façon raccourci cela nous dennerai

        return $this->createQueryBuilder('a')->getQuery()->getresult();
    }

    public function myfindOne($id)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }
    */

    /*Requete DQl
    public function myFindAllDQL()
    {
        $query = $this->_em->createQuery('SELECT a FROM CHARLYPlatformBundle:Advert a');
        $results = $query->getResult();

        return $results;
    }
    */
}
