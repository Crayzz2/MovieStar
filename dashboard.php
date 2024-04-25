<?php
    require_once ("templates/header.php");
    require_once ("models/User.php");
    require_once ("dao/UserDAO.php");
    require_once ("dao/MovieDAO.php");

    $user = new User();
    $userDao = new UserDao($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);

    $userData = $userDao->verifyToken(true);

    $userMovie = $movieDao->getMoviesByUserId($userData->id);
?>

    <div id="main-container" class="container-fluid">

        <h2 class="section-title">Dashboard</h2>
        <p class="section-description">Adicione ou atualize as informações dos filmes que você enviou.</p>
        <div class="col-md12" id="add-movie-container">
            <a href="<?= $BASE_URL ?>newmovie.php" class="btn card-btn"><i class="fas fa-plus"></i> Adicionar Filme</a>
        </div><br>
        <div class="col-md-12" id="movies-dashboard">
            <table class="table">
                <thead>
                    <tr>
                        <th scopes="col">#</th>
                        <th scopes="col">Título</th>
                        <th scopes="col">Nota</th>
                        <th scopes="col" class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($userMovie as $movie):?>
                        <tr>
                            <td scope="row"><?= $movie->id ?></td>
                            <td><a href="<?= $BASE_URL; ?>movie.php?id=<?= $movie->id ?>" class="table-movie-title"><?= $movie->title ?></a></td>
                            <td><i class="fas fa-star"></i> <?= $movie->rating ?></td>
                            <td class="actions-column">
                                <a href="<?= $BASE_URL; ?>editmovie.php?id=<?= $movie->id ?>" class="edit-btn">
                                    <i class="far fa-edit"></i> Editar
                                </a>
                                <form action="<?= $BASE_URL; ?>movie_process.php" method="POST">
                                    <input type="hidden" name="type" value="delete">
                                    <input type="hidden" name="id" value="<?= $movie->id ?>">
                                    <button type="submit" class="delete-btn">
                                        <i class="fas fa-times"></i> Deletar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
    require_once ("templates/footer.php");
?>