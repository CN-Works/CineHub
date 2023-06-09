<?php

require_once("db/DAO.php");

class MovieController {
    public function findAllMovies() {
        $dao = new DAO();

        $sql_request = "SELECT id_film, image_film, titre_film AS titre, synopsis_film AS summary, annee_film AS publish_date FROM film";

        $films = $dao->executeRequest($sql_request);

        require("views/movie/movielisting.php");
    }

    public function getMovieInformationsPage($movieId) {
        $dao = new DAO();
        
        // Get movie informations
        $filmSql = "SELECT f.id_film AS id_movie, re.id_realisateur AS id_producer, f.image_film AS image_movie,
                    f.titre_film AS title, YEAR(f.annee_film) AS year_production, pe.prenom AS producer_firstname,
                    pe.nom AS producer_lastname, f.duree_film AS duration, f.synopsis_film AS summary 
                    FROM film f
                    INNER JOIN realisateur re ON f.id_realisateur = re.id_realisateur
                    INNER JOIN personne pe ON re.id_personne = pe.id_personne
                    WHERE f.id_film = :movieId";
    
        $query_parameter = array(':movieId' => $movieId);
        $movieinfos = $dao->executeRequest($filmSql, $query_parameter);

        $informations = array();

        // Setting up data in a usable array (because of PDO object)
        foreach ($movieinfos->fetchAll() as $info) {
            $informations["title"] = $info["title"];
            $informations["id_movie"] = $info["id_movie"];
            $informations["id_producer"] = $info["id_producer"];
            $informations["image_movie"] = $info["image_movie"];
            $informations["year_production"] = $info["year_production"];
            $informations["producer_lastname"] = $info["producer_lastname"];
            $informations["producer_firstname"] = $info["producer_firstname"];
            $informations["duration"] = $info["duration"];
            $informations["summary"] = $info["summary"];
        }
        

        // Getting all genres of the movie
        $genresSql = "SELECT ge.nom_genre AS genre_name
                      FROM film f
                      INNER JOIN posseder po ON f.id_film = po.id_film
                      INNER JOIN genre ge ON po.id_genre = ge.id_genre
                      WHERE f.id_film = :movieId";
    
        $genres = $dao->executeRequest($genresSql, $query_parameter);

        $casting_request = "SELECT jo.id_film AS id_film, jo.id_role AS id_role, jo.id_acteur AS id_actor, pe.nom AS lastname, pe.prenom AS firstname, ro.nom_role AS rolename, f.titre_film AS title_movie
                    FROM jouer jo
                    INNER JOIN film f ON jo.id_film = f.id_film
                    INNER JOIN role_movie ro ON jo.id_role = ro.id_role
                    INNER JOIN acteur ac ON jo.id_acteur = ac.id_acteur
                    INNER JOIN personne pe ON ac.id_personne = pe.id_personne
                    WHERE f.id_film = :movieId";
        
        $casting = $dao->executeRequest($casting_request, $query_parameter);
    
        require "views/movie/movieinfos.php";
    }

    public function addMovieForm() {
        $dao = new DAO();

        $getAllGenres = "SELECT ge.id_genre AS id, ge.nom_genre AS genre_label FROM genre ge";
        $getAllProducers = "SELECT r.id_realisateur AS id, p.nom AS lastname, p.prenom AS firstname FROM realisateur r INNER JOIN personne p ON r.id_personne = p.id_personne";

        $availableGenres = $dao->executeRequest($getAllGenres);
        $availableProducer = $dao->executeRequest($getAllProducers);

        require("views/movie/addmovie.php");
    }

    public function addingMovie() {
        $dao = new DAO();

        if (isset($_POST['add'])) {

            // Production year
            $date = filter_input(INPUT_POST, 'prod_year', FILTER_SANITIZE_SPECIAL_CHARS);
            // $date = strtotime($date);

            // Image link
            $img_film = filter_input(INPUT_POST, 'imagelink', FILTER_VALIDATE_URL);

            // Title
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);

            // Duration in minutes
            $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);

            // Movie's summary
            $summary = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_SPECIAL_CHARS);

            // By default in my db, the id is always an integer
            $producer = filter_input(INPUT_POST, 'producer', FILTER_SANITIZE_NUMBER_INT);

            // $genres filter_var_array();

            $addingMovieQuery = "INSERT INTO film (titre_film, annee_film, duree_film, image_film, synopsis_film, id_realisateur, note_film) 
            VALUES (:title, :prod_year, :duration, :imagelink, :summary, :producer, :note)";

            $params = array(
                ":title" => $title,
                ":prod_year" => $date,
                ":duration" => $duration,
                ":imagelink" => $img_film,
                ":summary" => $summary,
                ":producer" => $producer,
                ":note" => 0.8
            );

            $new_movie = $dao->executeRequest($addingMovieQuery, $params);
            $lastId = $dao->getDB()->lastInsertId();

            header("location: http://localhost/CineHub/index.php?action=movieInformations&movieId=".$lastId);
        } else {
            header('location: http://localhost/CineHub/index.php?action=movieList');
        }
    }

    public function removeMovie($movieId) {
        $dao = new DAO();

        // Removing movie from "Film" table
        $removeMovie = "DELETE FROM Film WHERE id_Film = :movieId";
        $params = array(
            ":movieId" => $movieId,
        );

        $dao->executeRequest($removeMovie,$params);

        // $removeCasting = "DELETE FROM posseder WHERE id_film = :idFilm";

        header('location: http://localhost/CineHub/index.php?action=movieList');
    }

    public function updateMovieForm($movieId) {
        $dao = new DAO();

        // Get movie informations
        $filmSql = "SELECT f.id_film AS id_movie, re.id_realisateur AS id_producer, f.image_film AS image_movie,
                    f.titre_film AS title, YEAR(f.annee_film) AS year_production, pe.prenom AS producer_firstname,
                    pe.nom AS producer_lastname, f.duree_film AS duration, f.synopsis_film AS summary 
                    FROM film f
                    INNER JOIN realisateur re ON f.id_realisateur = re.id_realisateur
                    INNER JOIN personne pe ON re.id_personne = pe.id_personne
                    WHERE f.id_film = :movieId";
    
        $query_parameter = array(':movieId' => $movieId);
        $movieinfos = $dao->executeRequest($filmSql, $query_parameter);

        $informations = array();

        // Setting up data in a usable array (because of PDO object)
        foreach ($movieinfos->fetchAll() as $info) {
            $informations["title"] = $info["title"];
            $informations["id_movie"] = $info["id_movie"];
            $informations["id_producer"] = $info["id_producer"];
            $informations["image_movie"] = $info["image_movie"];
            $informations["year_production"] = $info["year_production"];
            $informations["producer_lastname"] = $info["producer_lastname"];
            $informations["producer_firstname"] = $info["producer_firstname"];
            $informations["duration"] = $info["duration"];
            $informations["summary"] = $info["summary"];
        }


        require "views/movie/updatemovie.php";
    }
}

?>