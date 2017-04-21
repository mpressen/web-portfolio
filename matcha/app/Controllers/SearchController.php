<?php
namespace App\Controllers;

use App\Models\Tag;
use App\UserList\UserList;
use App\Pagination\Pagination;
use App\Validation\Validator;

class SearchController extends Controller
{
	public function index( $request , $response , $args )
	{
        $qs = $request->getQueryParams();
        $user_loggued = $this->container->auth->user();

        $validator = new Validator();
 
        $tag = new Tag();
        $tags = [];
        foreach ($tag->getAllTags() as $t) {
            $tags[] = $t->name;
        }

        $user_list = new UserList( $user_loggued->getId() );
/*
 * Attraction
 */
        if (!empty($qs['attraction']))
        	$user_list->setNumAttraction($qs['attraction']);
        $qs['attraction'] = $user_list->getNumAttraction();
/*
 * Gender
 */
        if (!empty($qs['gender']))
        	$user_list->setNumGender($qs['gender']);
        $qs['gender'] = $user_list->getNumGender();
/*
 *  Age
 */
        if (!empty( $qs['age'] ))
        {
            $age = explode(',', $qs['age']);
            if ($validator->validationAge($age[0]))
                $qs['min_age'] = intval($age[0]);
            if ($validator->validationAge($age[1]))
                $qs['max_age'] = intval($age[1]);
        }
        if (!isset( $qs['min_age'] ) || !is_numeric( $qs['min_age'] ))
            $qs['min_age'] = 18;
        if (!isset( $qs['max_age'] ) || !is_numeric( $qs['max_age'] ))
            $qs['max_age'] = 45;
        $user_list->setMinAge( $qs['min_age'] );
        $user_list->setMaxAge( $qs['max_age'] );
/*
 *  Popularity
 */
        if (!empty( $qs['popularity'] ))
        {
            $popularity = explode(',', $qs['popularity']);
            if ($validator->validationPopularity($popularity[0]))
                $qs['min_pop'] = intval($popularity[0]);
            if ($validator->validationPopularity($popularity[1]))
                $qs['max_pop'] = intval($popularity[1]);
        }
        if (!isset( $qs['min_pop'] ) || !is_numeric( $qs['min_pop'] ))
            $qs['min_pop'] = 0;
        if (!isset( $qs['max_pop'] ) || !is_numeric( $qs['max_pop'] ))
            $qs['max_pop'] = 1000;
        $user_list->setMinPopularity( $qs['min_pop'] );
        $user_list->setMaxPopularity( $qs['max_pop'] );
/*
 * Tags
 */
        if (!empty( $qs['tags'] ))
        {
            $qs['tags'] = array_filter(explode(',', $qs['tags']), function ($v) use ($validator) { return $validator->validationTag($v); });
            if (is_array($qs['tags']))
                $user_list->setTags( $qs['tags'] );
        }
/*
 *  Latitude, longitude
 */
        if (!empty( $qs['location'] ) && $validator->validationLatitude( $qs['latitude'] ) && $validator->validationLongitude( $qs['longitude'] ))
	        $user_list->setLocation( $qs['latitude'], $qs['longitude'] );
/*
 * Location range
 */
        if (isset( $qs['range'] ))
            $user_list->setNumRange( $qs['range'] );
        $qs['range'] = $user_list->getNumRange();
/*
 * Order
 */
        if (isset( $qs['order'] ))
            $user_list->setNumOrder( $qs['order'] );
        $qs['order'] = $user_list->getNumOrder();
/*
 * Username
 */
        if (!empty( $qs['username'] ))
            $user_list->setUsername( $qs['username'] );
/*
 * User in Visited | Liked
 */
        if (isset( $qs['groupuser'] ))
            $user_list->setGroupUser( $qs['groupuser'] );
/*
 * Rows number and Offset
 */
        if (isset( $qs['nb'] ))
            $user_list->setNumLimit( $qs['nb'] );
        $qs['nb'] = $user_list->getNumLimit();

        if (!isset( $qs['page'] ) || !filter_var( $qs['page'] , FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]))
            $qs['page'] = 1;
        $user_list->setOffset( $qs['page'] );

        $list = $user_list->getList();

        $pagination = new Pagination( $qs['page'] , $user_list->getLimit() , $user_list->getFoundRows() );

		return $this->view->render( $response, 'search.twig', ['args' => $qs, 'tags' => $tags, "qs" => $request->getUri()->getQuery(), "pagination" => $pagination, "user_list" => $list] );
	}
}