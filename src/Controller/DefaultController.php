<?php

namespace App\Controller;
use App\Entity\User;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;


class DefaultController extends Controller
{	

    /**
     * @Route("/", name="default")
     @Route("/{route}", name="vue_pages", requirements={"vueRouting"="^(?!api|phpMyAdmin|_(profiler|wdt)).*"})

     */
    public function index()
    {


        // $address = "Philippines+CagayandeOro";
        // $url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Philippines&key=AIzaSyB7t_QJxy_V-ReIGtUc4Ob_xL_LMo_UO2k";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // $response = curl_exec($ch);
        // curl_close($ch);

        // $response_a = json_decode($response);

        
// echo $lat = $response_a->results[0]->geometry->location->lat;
// echo "<br />";
// echo $long = $response_a->results[0]->geometry->location->lng;



         return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }





   /**
     * @Route("/api/login", name="loginaction_route")
     */
    public function loginAction(Request $request)
    {
    	$userExists = false;
    	$data = json_decode($request->getContent(), true);

    	$user = $this->getDoctrine()->getRepository("App:User")->findOneBy([
		    'email' => $data['username'],
		    'password' => md5($data['password'])
		]);


		if($user) {
			$userExists = true;
		} else {
			return  new JsonResponse(array('success' => false, "user_id" => false, "user_type" => false));
		}


        return  new JsonResponse(array('success' => $userExists, "user_id" => $user->getId(), "user_type" => $user->getUserType()));
    }


    /**
     * @Route("/api/createuser", name="createuser_route")
     */
    public function createUserAction(Request $request)
    {
    	$data = json_decode($request->getContent(), true);
		

		$user = $this->getDoctrine()->getRepository("App:User")->findOneBy([
		    'email' => $data['email'],
		]);

		if($user) {
			return new JsonResponse(['error' => 'Email address is already registered on the system.', 'success' => false], 200);
		}
		// you can fetch the EntityManager via $this->getDoctrine()
	    // or you can add an argument to your action: createAction(EntityManagerInterface $entityManager)
	    $U = new User();
	    $U->setName($data["name"]);
        $U->setEmail($data["email"]);
        $U->setPassword(md5($data["password"]));
        $U->setUserType("user");
        $U->setGender($data["gender"]);
        $U->setAddress($data["address"]);
        $U->setDescription($data["description"]);

        $blob_file= mb_check_encoding($data['cropImg'], 'UTF-8') ? $data['cropImg']: utf8_encode($data['cropImg']);
        $U->setProfileImage($blob_file);

        try {
	        $em = $this->getDoctrine()->getManager();
	        $em->persist($U);
	        $em->flush();
	        return new JsonResponse(['error' => '', 'success' => "You have successfully Registered!"], 200);
	    } catch(\Doctrine\DBAL\DBALException $e) {
	        return new JsonResponse(['error' => 'Something is wrong, please contact administrator'], 400);
	    }
    }

    /**
     * @Route("/api/updateuser", name="updateuser_route")
     */
    public function updateUserAction(Request $request)
    {
    	$data = json_decode($request->getContent(), true);
		
		// CHECK FOR EMAIL IF IT EXISTS FROM OTHER MAILS NATIVE QUERY FOR NOW
		$conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = 'SELECT id FROM user WHERE id != '.$data['id'].' and email ="'.$data['email'].'"';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $emailExists = count($stmt->fetchAll()) > 0;

		if($emailExists) {
			return new JsonResponse(['error' => 'Email address is already used by another user.', 'success' => false], 200);
		}

		// you can fetch the EntityManager via $this->getDoctrine()
	    // or you can add an argument to your action: createAction(EntityManagerInterface $entityManager)

	    $U = $this->getDoctrine()->getRepository("App:User")->find($data['id']);
	    $U->setName($data["name"]);
        $U->setEmail($data["email"]);
        if(isset($data["password"]))
            $U->setPassword(md5($data["password"]));
        $U->setUserType("user");
        $U->setGender($data["gender"]);
        $U->setAddress($data["address"]);
        $U->setDescription($data["description"]);
        
        if(isset($data["cropImg"]))
            $U->setProfileImage($data['cropImg']);


        try {
	        $em = $this->getDoctrine()->getManager();
	        // $em->persist($U);
	        $em->flush();
	        return new JsonResponse(['error' => '', 'success' => "Account successfully updated!"], 200);
	    } catch(\Doctrine\DBAL\DBALException $e) {
	        return new JsonResponse(['error' => 'Something is wrong, please contact administrator'], 400);
	    }
    }


    /**
     * @Route("/api/lists", name="listaction_route")
     */
    public function listAction(Request $request)
    {
    	$userExists = false;
    	$data = json_decode($request->getContent(), true);

    	$users = $this->getDoctrine()->getRepository("App:User")->findAll();

    	$items = array();
    	foreach($users as $u) {

    		if($u->getUserType() == "admin")
    			continue;

    		$items[] = array(
    			"id" => $u->getId(),
    			"name" => $u->getName(),
    			"email" => $u->getEmail(),
                "gender" => $u->getGender(),
    			// "gender" => $u->getGender() == "m" ? "Male" : "Female",
    			"description" => $u->getDescription(),
    			"address" => $u->getAddress(),

    		);    		
    	}

        return  new JsonResponse(array('success' => $userExists, "items" => $items));
    }

    /**
     * @Route("/api/listsjobs", name="listsjobsaction_route")
     */
    public function listJobsAction(Request $request)
    {
        $userExists = false;
        $data = json_decode($request->getContent(), true);

        // LOAD NATIVELY A TABLE
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = 'SELECT * FROM `jobs`';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // $emailExists = count($stmt->fetchAll()) > 0;
        $jobs = $stmt->fetchAll();


        $items = array();
        foreach($jobs as $j) {
            $lat1 = $data['lat'];
            $lon1 = $data['long'];
            $lat2  = $j['lat'];
            $lon2 = $j['long'];

            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            // $unit = strtoupper($unit);

            $j['distance'] = number_format($miles * 1.609344, 2)." Km";

            $items[] = $j;
        }

        return  new JsonResponse(array('success' => true, "items" => $items));
    }




    /**
     * @Route("/api/profile", name="profile_route")
     */
    public function userAccountAction(Request $request)
    {
    	$data = json_decode($request->getContent(), true);

    	$user = $this->getDoctrine()->getRepository("App:User")->find($data['id']);

    	$data = array(
    			"id" => $user->getId(),
    			"name" => $user->getName(),
    			"email" => $user->getEmail(),
    			"gender" => $user->getGender(),
    			"description" => $user->getDescription(),
    			"address" => $user->getAddress(),
    			"image_blob" => $user->getProfileImage(),

    		);  
    	
        return  new JsonResponse(array("data" => $data));
    }


    /**
     * @Route("/api/delete", name="delete_route")
     */
    public function deleteAction(Request $request)
    {
    	$data = json_decode($request->getContent(), true);

    	$user = $this->getDoctrine()->getRepository("App:User")->find($data['id']);
    	
    	try {
	        $em = $this->getDoctrine()->getManager();
	        $em->remove($user);
	        $em->flush();
	        return new JsonResponse(['error' => '', 'success' => "Account successfully deleted!"], 200);
	    } catch(\Doctrine\DBAL\DBALException $e) {
	        return new JsonResponse(['error' => 'Something is wrong, please contact administrator'], 400);
	    }
    	
        return  new JsonResponse(array());
    }


     /**
     * @Route("/api/upload", name="upload_route")
     */
    public function uploadAction(Request $request)
    {

        $conn = $this->getDoctrine()->getManager()->getConnection();
        // $data = json_decode($request->getContent(), true);


        $fileName = time()."__file.csv";
        $file_ = $request->files->get('file');
        // moves the file to the directory where usuarios are stored
        $file_->move(
            $this->getParameter('upload_csv'),
            $fileName
        );


        $csv_D = file_get_contents($this->getParameter('upload_csv')."/".$fileName);

        $exp_D = explode("\n", $csv_D);


        $batch = 1000;
        $cnt = 0;

        $batch_insert = array();

        try { 
            $batch_cnt = 0;
            foreach($exp_D as $row) {
                $row = explode(",", $row);

                $row[0] = str_replace('"', "",  $row[0]);
                $row[7] = '"'.date("Y-m-d h:i:s").'"';
                $row[8] = '"'.date("Y-m-d h:i:s").'"';

                if($row)
                    $batch_insert[]  = "(".implode(",", $row).")";

                
                if( count($batch_insert) == $batch ) {
                    $conn = $this->getDoctrine()->getManager()->getConnection();
                    $sql = "REPLACE INTO jobs VALUES ".implode(",", $batch_insert).";";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    $batch_cnt += count($batch_insert);
                    $batch_insert = array();
                    $cnt = 0;
                }


                $cnt++;
                
            }

            if( count($batch_insert) ) {
                $conn = $this->getDoctrine()->getManager()->getConnection();
                $sql = "REPLACE INTO jobs VALUES ".implode(",", $batch_insert).";";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $batch_cnt += count($batch_insert);
                $batch_insert = array();
                $cnt = 0;
            }
       

            return  new JsonResponse(array('success' => true, 'count' => $batch_cnt));

        } catch(\Doctrine\DBAL\DBALException $e) {
            return new JsonResponse(['error' => 'Something is wrong, with the emport'], 400);
        }

    }


}
