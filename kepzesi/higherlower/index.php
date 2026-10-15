<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/bb8ffdf1b9.js" crossorigin="anonymous"></script>
  <title>Magasabb vagy alacsonyabb?</title>

  <?php

  session_start();

  require_once __DIR__ . '/../includes/database.php';
  $conn = kepzesi_database_connection('higherorlowergame');
  include("server/functions.php");

  $user_data = check_login($conn);




  ?>

</head>

<!-- Sidebar szöveg kilóg transition alatt -->
<!-- Reszponzivitás -->

<body class="homePage">
  <div class="sidebarbg">
    <div class="sidebar closed">
      <div class="sidebar-header">
        <a href="javascript:void(0)" id="sidebar-toggle">
          <i class="fa-solid fa-bars "></i>
        </a>
      </div>
      <div class="sidebar-content">
        <ul class="sb-list">
          <li class="sb-list-item">
            <a id="leaderboardBtn" aria-label="Ranglista">
              <i class="fa-solid fa-trophy"></i>
              <span>Ranglista</span>
            </a>
          </li>
          <li class="sb-list-item sub-leaderboard-item">
            <a aria-label="Spotify mód" onclick="changeLeaderboard('spotify')">
              <i class="fa-brands fa-spotify"></i>
              <span>Spotify mód</span>
            </a>
          </li>
          <li class="sb-list-item sub-leaderboard-item">
            <a aria-label="Országok mód" onclick="changeLeaderboard('countries')">
              <i class="fa-solid fa-earth-americas"></i>
              <span>Országok</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <ul class="sb-list">
          <li class="sb-list-item">
            <a id="profileBtn" aria-label="Profil">
              <i class="fa-solid fa-user"></i>
              <span>Profil</span>
            </a>
          </li>
          <li class="sb-list-item">
            <a href="server/logout.php" aria-label="Kijelentkezés">
              <i class="fa-solid fa-right-from-bracket"></i>
              <span>Kijelentkezés</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>



  <div class="page-content">
    <div class="container d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
      <a class="gameBtns spotify" onclick="startGame('spotify')" href="game.php"><i class="fa-brands fa-spotify"></i>Spotify</a>
      <a class="gameBtns countries" onclick="startGame('countries')" href="game.php"><i
          class="fa-solid fa-earth-americas"></i>
          Országok</a>
    </div>



    <!--     <div class="playbtn">
      <a href="game.php"><i class="fa-solid fa-circle-play"></i></a>
    </div> -->
  </div>

  <div class="profile-card" data-bs-theme="dark" hidden>
    <div class="profile-container text-center m-3">
      <form method="POST" action="" id="profileForm">
        <div class="col">
          <div class="row">
            <p class="yourProfile">Profilod</p>
          </div>
          <div class="row">
            <i class="fa-regular fa-user"></i>
            <p class="username fs-4 showUserData"><?php echo $user_data['username']; ?></p>
          </div>
          <div class="row my-3 changeUserData" hidden>
            <div class="col-auto align-self-center">
              <label for="changeUserData" class="form-label ">Felhasználónév</label>
            </div>
            <div class="col">
              <input type="text" name="username" class="form-control  w-100 " id="changeUserData"
                aria-describedby="changeUsername" value="<?php echo $user_data['username'] ?>">
            </div>
          </div>
          <div class="row">
            <p class="userEmail showUserData">
              <?php if ($user_data['is_guest']): ?>
                Vendégként játszol. A pontszámaid nem kerülnek mentésre a ranglistára.
              <?php else: ?>
                <i class="fa-regular fa-envelope m-1"></i>E-mail-cím:
                <?php echo $user_data['email']; ?>
              <?php endif; ?>
            </p>
          </div>
          <div class="row my-3 changeUserData" hidden>
            <div class="col-auto align-self-center">
              <label for="changeUserData" class="form-label ">E-mail-cím</label>
            </div>
            <div class="col">
              <input type="text" name="email" class="form-control w-100 " id="changeUserData"
                aria-describedby="changeEmail" value="<?php echo $user_data['email'] ?>">
            </div>
          </div>
          <div class="row">
            <div class="country showUserData" data-country="<?php echo htmlspecialchars($user_data['country']); ?>" <?php echo $user_data['is_guest'] ? 'hidden' : ''; ?>>
              <img id="countryFlag">
            </div>
          </div>
          <div class="row my-3 changeUserData" hidden>
            <div class="col-auto align-self-center">
              <label for="selectCountry" class="form-label">Ország kiválasztása:</label>
            </div>
            <div class="col">
              <div class="dropdown">
                <div class="selectCountry">
                  <div>
                    <img class="flag"
                      src="https://purecatamphetamine.github.io/country-flag-icons/3x2/<?php echo $user_data['country']; ?>.svg">
                  </div>
                  <div class="arrow"><i class="fa-solid fa-chevron-down"></i></div>
                </div>
                <ul class="countryMenu">
                  <li data-value="AF"><img>Afghanistan</li>
                  <li data-value="AL"><img>Albania</li>
                  <li data-value="DZ"><img>Algeria</li>
                  <li data-value="AD"><img>Andorra</li>
                  <li data-value="AO"><img>Angola</li>
                  <li data-value="AG"><img>Antigua and Barbuda</li>
                  <li data-value="AR"><img>Argentina</li>
                  <li data-value="AM"><img>Armenia</li>
                  <li data-value="AU"><img>Australia</li>
                  <li data-value="AT"><img>Austria</li>
                  <li data-value="AZ"><img>Azerbaijan</li>
                  <li data-value="BS"><img>Bahamas</li>
                  <li data-value="BH"><img>Bahrain</li>
                  <li data-value="BD"><img>Bangladesh</li>
                  <li data-value="BB"><img>Barbados</li>
                  <li data-value="BY"><img>Belarus</li>
                  <li data-value="BE"><img>Belgium</li>
                  <li data-value="BZ"><img>Belize</li>
                  <li data-value="BJ"><img>Benin</li>
                  <li data-value="BT"><img>Bhutan</li>
                  <li data-value="BO"><img>Bolivia</li>
                  <li data-value="BA"><img>Bosnia and Herzegovina</li>
                  <li data-value="BW"><img>Botswana</li>
                  <li data-value="BR"><img>Brazil</li>
                  <li data-value="BN"><img>Brunei</li>
                  <li data-value="BG"><img>Bulgaria</li>
                  <li data-value="BF"><img>Burkina Faso</li>
                  <li data-value="BI"><img>Burundi</li>
                  <li data-value="CV"><img>Cabo Verde</li>
                  <li data-value="KH"><img>Cambodia</li>
                  <li data-value="CM"><img>Cameroon</li>
                  <li data-value="CA"><img>Canada</li>
                  <li data-value="CF"><img>Central African Republic</li>
                  <li data-value="TD"><img>Chad</li>
                  <li data-value="CL"><img>Chile</li>
                  <li data-value="CN"><img>China</li>
                  <li data-value="CO"><img>Colombia</li>
                  <li data-value="KM"><img>Comoros</li>
                  <li data-value="CD"><img>Congo (DRC)</li>
                  <li data-value="CG"><img>Congo (Republic)</li>
                  <li data-value="CR"><img>Costa Rica</li>
                  <li data-value="CI"><img>Côte d’Ivoire</li>
                  <li data-value="HR"><img>Croatia</li>
                  <li data-value="CU"><img>Cuba</li>
                  <li data-value="CY"><img>Cyprus</li>
                  <li data-value="CZ"><img>Czechia</li>
                  <li data-value="DK"><img>Denmark</li>
                  <li data-value="DJ"><img>Djibouti</li>
                  <li data-value="DM"><img>Dominica</li>
                  <li data-value="DO"><img>Dominican Republic</li>
                  <li data-value="EC"><img>Ecuador</li>
                  <li data-value="EG"><img>Egypt</li>
                  <li data-value="SV"><img>El Salvador</li>
                  <li data-value="GQ"><img>Equatorial Guinea</li>
                  <li data-value="ER"><img>Eritrea</li>
                  <li data-value="EE"><img>Estonia</li>
                  <li data-value="SZ"><img>Eswatini</li>
                  <li data-value="ET"><img>Ethiopia</li>
                  <li data-value="FJ"><img>Fiji</li>
                  <li data-value="FI"><img>Finland</li>
                  <li data-value="FR"><img>France</li>
                  <li data-value="GA"><img>Gabon</li>
                  <li data-value="GM"><img>Gambia</li>
                  <li data-value="GE"><img>Georgia</li>
                  <li data-value="DE"><img>Germany</li>
                  <li data-value="GH"><img>Ghana</li>
                  <li data-value="GR"><img>Greece</li>
                  <li data-value="GD"><img>Grenada</li>
                  <li data-value="GT"><img>Guatemala</li>
                  <li data-value="GN"><img>Guinea</li>
                  <li data-value="GW"><img>Guinea-Bissau</li>
                  <li data-value="GY"><img>Guyana</li>
                  <li data-value="HT"><img>Haiti</li>
                  <li data-value="HN"><img>Honduras</li>
                  <li data-value="HU"><img>Hungary</li>
                  <li data-value="IS"><img>Iceland</li>
                  <li data-value="IN"><img>India</li>
                  <li data-value="ID"><img>Indonesia</li>
                  <li data-value="IR"><img>Iran</li>
                  <li data-value="IQ"><img>Iraq</li>
                  <li data-value="IE"><img>Ireland</li>
                  <li data-value="IL"><img>Israel</li>
                  <li data-value="IT"><img>Italy</li>
                  <li data-value="JM"><img>Jamaica</li>
                  <li data-value="JP"><img>Japan</li>
                  <li data-value="JO"><img>Jordan</li>
                  <li data-value="KZ"><img>Kazakhstan</li>
                  <li data-value="KE"><img>Kenya</li>
                  <li data-value="KI"><img>Kiribati</li>
                  <li data-value="KW"><img>Kuwait</li>
                  <li data-value="KG"><img>Kyrgyzstan</li>
                  <li data-value="LA"><img>Laos</li>
                  <li data-value="LV"><img>Latvia</li>
                  <li data-value="LB"><img>Lebanon</li>
                  <li data-value="LS"><img>Lesotho</li>
                  <li data-value="LR"><img>Liberia</li>
                  <li data-value="LY"><img>Libya</li>
                  <li data-value="LI"><img>Liechtenstein</li>
                  <li data-value="LT"><img>Lithuania</li>
                  <li data-value="LU"><img>Luxembourg</li>
                  <li data-value="MG"><img>Madagascar</li>
                  <li data-value="MW"><img>Malawi</li>
                  <li data-value="MY"><img>Malaysia</li>
                  <li data-value="MV"><img>Maldives</li>
                  <li data-value="ML"><img>Mali</li>
                  <li data-value="MT"><img>Malta</li>
                  <li data-value="MH"><img>Marshall Islands</li>
                  <li data-value="MR"><img>Mauritania</li>
                  <li data-value="MU"><img>Mauritius</li>
                  <li data-value="MX"><img>Mexico</li>
                  <li data-value="FM"><img>Micronesia</li>
                  <li data-value="MD"><img>Moldova</li>
                  <li data-value="MC"><img>Monaco</li>
                  <li data-value="MN"><img>Mongolia</li>
                  <li data-value="ME"><img>Montenegro</li>
                  <li data-value="MA"><img>Morocco</li>
                  <li data-value="MZ"><img>Mozambique</li>
                  <li data-value="MM"><img>Myanmar</li>
                  <li data-value="NA"><img>Namibia</li>
                  <li data-value="NR"><img>Nauru</li>
                  <li data-value="NP"><img>Nepal</li>
                  <li data-value="NL"><img>Netherlands</li>
                  <li data-value="NZ"><img>New Zealand</li>
                  <li data-value="NI"><img>Nicaragua</li>
                  <li data-value="NE"><img>Niger</li>
                  <li data-value="NG"><img>Nigeria</li>
                  <li data-value="KP"><img>North Korea</li>
                  <li data-value="MK"><img>North Macedonia</li>
                  <li data-value="NO"><img>Norway</li>
                  <li data-value="OM"><img>Oman</li>
                  <li data-value="PK"><img>Pakistan</li>
                  <li data-value="PW"><img>Palau</li>
                  <li data-value="PA"><img>Panama</li>
                  <li data-value="PG"><img>Papua New Guinea</li>
                  <li data-value="PY"><img>Paraguay</li>
                  <li data-value="PE"><img>Peru</li>
                  <li data-value="PH"><img>Philippines</li>
                  <li data-value="PL"><img>Poland</li>
                  <li data-value="PT"><img>Portugal</li>
                  <li data-value="QA"><img>Qatar</li>
                  <li data-value="RO"><img>Romania</li>
                  <li data-value="RU"><img>Russia</li>
                  <li data-value="RW"><img>Rwanda</li>
                  <li data-value="KN"><img>Saint Kitts and Nevis</li>
                  <li data-value="LC"><img>Saint Lucia</li>
                  <li data-value="WS"><img>Samoa</li>
                  <li data-value="SM"><img>San Marino</li>
                  <li data-value="ST"><img>Sao Tome and Principe</li>
                  <li data-value="SA"><img>Saudi Arabia</li>
                  <li data-value="SN"><img>Senegal</li>
                  <li data-value="RS"><img>Serbia</li>
                  <li data-value="SC"><img>Seychelles</li>
                  <li data-value="SL"><img>Sierra Leone</li>
                  <li data-value="SG"><img>Singapore</li>
                  <li data-value="SK"><img>Slovakia</li>
                  <li data-value="SI"><img>Slovenia</li>
                  <li data-value="SB"><img>Solomon Islands</li>
                  <li data-value="SO"><img>Somalia</li>
                  <li data-value="ZA"><img>South Africa</li>
                  <li data-value="KR"><img>South Korea</li>
                  <li data-value="SS"><img>South Sudan</li>
                  <li data-value="ES"><img>Spain</li>
                  <li data-value="LK"><img>Sri Lanka</li>
                  <li data-value="SD"><img>Sudan</li>
                  <li data-value="SR"><img>Suriname</li>
                  <li data-value="SE"><img>Sweden</li>
                  <li data-value="CH"><img>Switzerland</li>
                  <li data-value="SY"><img>Syria</li>
                  <li data-value="TJ"><img>Tajikistan</li>
                  <li data-value="TZ"><img>Tanzania</li>
                  <li data-value="TH"><img>Thailand</li>
                  <li data-value="TL"><img>Timor-Leste</li>
                  <li data-value="TG"><img>Togo</li>
                  <li data-value="TO"><img>Tonga</li>
                  <li data-value="TT"><img>Trinidad and Tobago</li>
                  <li data-value="TN"><img>Tunisia</li>
                  <li data-value="TR"><img>Turkey</li>
                  <li data-value="TM"><img>Turkmenistan</li>
                  <li data-value="TV"><img>Tuvalu</li>
                  <li data-value="UG"><img>Uganda</li>
                  <li data-value="UA"><img>Ukraine</li>
                  <li data-value="AE"><img>United Arab Emirates</li>
                  <li data-value="GB"><img>United Kingdom</li>
                  <li data-value="US"><img>United States</li>
                  <li data-value="UY"><img>Uruguay</li>
                  <li data-value="UZ"><img>Uzbekistan</li>
                  <li data-value="VU"><img>Vanuatu</li>
                  <li data-value="VA"><img>Vatican City</li>
                  <li data-value="VE"><img>Venezuela</li>
                  <li data-value="VN"><img>Vietnam</li>
                  <li data-value="YE"><img>Yemen</li>
                  <li data-value="ZM"><img>Zambia</li>
                  <li data-value="ZW"><img>Zimbabwe</li>
                </ul>
              </div>
              <input type="hidden" name="country" id="countryInput" value="<?php echo $user_data['country']; ?>">
            </div>

          </div>
          <div class="row">
            <div class="line w-75"></div>
          </div>
          <div class="row">
            <?php if ($user_data['is_guest']): ?>
              <p>Regisztrálj, hogy elmentsd a pontszámaidat és szerkeszthesd a profilodat.</p>
            <?php else: ?>
              <button class="profileBtn" id="editProfileBtn" type="button">Profil szerkesztése</button>
            <?php endif; ?>
          </div>
          <div class="row changeUserData" hidden>
            <div class="col">
              <button class="profileBtn" id="cancelChanges" type="button">Mégse</button>
            </div>
            <div class="col">
              <button class="profileBtn" name="saveChanges" type="submit" id="saveChanges">Módosítások mentése</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php
  if ($_SERVER['REQUEST_METHOD'] == "POST" && !$user_data['is_guest'] && isset($_POST['saveChanges'])) {
    $updates = [];

    $username = $_POST['username'];
    $email = $_POST['email'];
    $country = $_POST['country'];



    $values = [];
    if ($username !== $user_data['username']) {
      $updates[] = 'username = ?';
      $values[] = $username;
    }

    if ($email !== $user_data['email']) {
      $updates[] = 'email = ?';
      $values[] = $email;
    }

    if ($country !== $user_data['country']) {
      $updates[] = 'country = ?';
      $values[] = $country;
    }

    if (!empty($updates)) {
      $sql = "UPDATE higherorlower_users
                SET " . implode(', ', $updates) . " 
                WHERE user_id = ?";

      $values[] = $user_data['user_id'];
      $statement = $conn->prepare($sql);
      if ($statement->execute($values)) {
        echo "<script>window.location.href = window.location.href;</script>";
        exit();
      } else {
        echo "Hiba történt az adatok mentésekor.";
      }
    }
  }
  ?>



  <div class="leaderboard">
    <table>
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th>Felhasználónév</th>
          <th class="text-center">Pontszám</th>
          <th>Játékmód</th>
        </tr>
      </thead>
      <tbody>
        <!--         <tr>
          <td>1</td>
          <td class="user">
            <div class="userCountry">
              <img src="https://purecatamphetamine.github.io/country-flag-icons/3x2/HU.svg">
            </div>
            User1
          </td>
          <td>1000</td>
        </tr> -->
        <?php

        $sql = "SELECT u.username, u.country, l.score, l.mode
                FROM higherorlower_leaderboard l
                JOIN higherorlower_users u ON l.user_id = u.user_id
                ORDER BY l.score DESC
                LIMIT 20";

        $result = $conn->query($sql);






        $place = 0;
        foreach ($result->fetchAll() as $row) {
            $place++;
            $modeIcon = $row['mode'] === 'spotify'
              ? "<i title='Spotify mód' aria-label='Spotify mód' style='color: #1DB954;' class='fa-brands fa-spotify'></i>"
              : "<i title='Országok mód' aria-label='Országok mód' style='color: #67edff;' class='fa-solid fa-earth-americas'></i>";
            echo "<tr>";
            echo "<td>" . $place . "</td>";
            echo "<td>";
            echo "<div class='userCountry text-left'>";
            echo "<img src='https://purecatamphetamine.github.io/country-flag-icons/3x2/" . $row['country'] . ".svg'>";
            echo "<span>" . $row['username'] . "</span>";
            echo "</div>";
            echo "</td>";
            echo "<td>" . $row['score'] . "</td>";
            echo "<td>" . $modeIcon . "</td>";
            echo "</tr>";
        }
        if ($place === 0) {
          echo "<tr><td colspan='4' class='text-center'>Még nincs megjeleníthető eredmény.</td></tr>";
        }
        ?>

      </tbody>
    </table>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>
  <script>
    let sidebar = document.querySelector('.sidebar')
    let sidebartoggle = document.getElementById("sidebar-toggle")
    let profileBtn = document.getElementById("profileBtn")
    let profileCard = document.querySelector('.profile-card')
    let editProfileBtn = document.getElementById("editProfileBtn")
    let showUserData = document.querySelectorAll(".showUserData")
    let changeUserData = document.querySelectorAll(".changeUserData")


    let leaderboardBtn = document.getElementById("leaderboardBtn")
    let leaderboard = document.querySelector('.leaderboard')


    let countryDiv = document.querySelector('.country')
    let country = countryDiv.dataset.country
    let countryFlag = document.getElementById('countryFlag')
    if (countryFlag && country) {
      countryFlag.src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${country}.svg`
    }

    let currentLeaderboardMode = ""
    let profileForm = document.getElementById("profileForm")


    sidebartoggle.addEventListener("click", function () {
      sidebar.classList.toggle("closed")

      if (sidebar.classList.contains("closed")) {
        document.body.classList.remove("sidebar-open")
      } else {
        document.body.classList.add("sidebar-open")
      }

      if (leaderboard.classList.contains("activelb")) {
        leaderboard.classList.toggle("closed")
      }
    })

    profileBtn.addEventListener("click", function () {
      if (profileCard.hidden) {
        profileCard.hidden = false
      } else {
        profileCard.hidden = true
      }
    })

    let cancelChanges = document.getElementById("cancelChanges")

    if (editProfileBtn) {
      editProfileBtn.addEventListener("click", function () {
        editProfileBtn.hidden = true
        showUserData.forEach(element => {
          element.hidden = true
        })
        changeUserData.forEach(element => {
          element.hidden = false
        })
      })
    }


    if (cancelChanges) {
      cancelChanges.addEventListener("click", function () {
        document.querySelector(".flag").src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${country}.svg`;
        profileForm.reset()
        profileCard.hidden = true
        if (editProfileBtn) {
          editProfileBtn.hidden = false
        }
        showUserData.forEach(element => {
          element.hidden = false
        })
        changeUserData.forEach(element => {
          element.hidden = true
        })
      })
    }

    const subLBItems = document.querySelectorAll(".sub-leaderboard-item")

    leaderboardBtn.addEventListener("click", function () {
      const isActive = leaderboard.classList.contains("activelb");

      if (!isActive) {
        currentLeaderboardMode = "mixed";
        changeLeaderboard(currentLeaderboardMode);
        leaderboard.classList.add("activelb");
        subLBItems.forEach(item => item.classList.add('visible'));
        if (!sidebar.classList.contains("closed")) {
          leaderboard.classList.remove("closed");
        }
        document.body.classList.add("leaderboard-open");
      } else {
        leaderboard.classList.remove("activelb");
        subLBItems.forEach(item => item.classList.remove("visible"));
        document.body.classList.remove("leaderboard-open")
      }
    })



    function changeLeaderboard(mode) {
      currentLeaderboardMode = mode

      fetch("server/changeLeaderboard_api.php?mode=" + mode, {
        method: "POST",
      })
        .then((response) => response.text())
        .then((data) => {
          const tbody = document.querySelector(".leaderboard tbody")
          tbody.innerHTML = data;
        })
        .catch((error) => console.error("Hiba a ranglista betöltésekor:", error))
    }

    /*     const subLBLinks = document.querySelectorAll(".sub-leaderboard-item a");
    
        subLBLinks.forEach(link => {
          link.addEventListener("click", function () {
            subLBLinks.forEach(l => l.parentElement.classList.remove("selected"));
            this.parentElement.classList.add("selected");
          });
    }); */




    function startGame(mode) {
      sessionStorage.setItem('gameMode', mode)
    }



    let selectCountry = document.querySelector('.selectCountry')
    let arrow = document.querySelector('.arrow')
    let countryMenu = document.querySelector('.countryMenu')
    let options = document.querySelectorAll('.countryMenu li')
    let flag = document.querySelector('.flag')
    let listItems = document.querySelectorAll('.countryMenu li')

    listItems.forEach(item => {
      const dataValue = item.getAttribute('data-value')
      const img = item.querySelector('img')
      if (dataValue && img) {
        img.classList.add("menuFlag" + dataValue, "menuFlag")
      }

      item.querySelector('img').src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${dataValue}.svg`
      const countryName = new Intl.DisplayNames(['hu'], { type: 'region' }).of(dataValue)
      const textNode = [...item.childNodes].find(node => node.nodeType === Node.TEXT_NODE)
      if (textNode && countryName) {
        textNode.textContent = countryName
      }
    })


    selectCountry.addEventListener('click', () => {

      selectCountry.classList.toggle('selectedCountry-clicked')
      arrow.classList.toggle('arrow-rotate')
      countryMenu.classList.toggle('countryMenu-open')
    })

    options.forEach(option => {
      option.addEventListener('click', () => {

        flag.src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${option.dataset.value}.svg`
        selectCountry.classList.remove('selectedCountry-clicked')
        arrow.classList.remove('arrow-rotate')
        countryMenu.classList.remove('countryMenu-open')
        countryInput.value = option.dataset.value
        options.forEach(opt => {
          opt.classList.remove('activeCountry')
        })
        option.classList.add('activeCountry')
      })
    })

  </script>
</body>

</html>