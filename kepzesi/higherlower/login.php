<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css">
    <script src="https://kit.fontawesome.com/bb8ffdf1b9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Bejelentkezés</title>

    <?php

    session_start();

    require_once __DIR__ . '/../includes/database.php';
    $conn = kepzesi_database_connection('higherorlowergame');
    include("server/functions.php");

    $form_mode = $_POST['formtype'] ?? 'login';

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['guest'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = generate__uuid();
            $_SESSION['is_guest'] = true;
            header("Location: index.php");
            die;
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $country = $_POST['country'] ?? '';

        if ($form_mode == 'signup') {
            if (!empty($username) && !empty($password) && !empty($email) && !is_numeric($username)) {


                //mentés az adatbázisba
                $user_id = generate__uuid();
                $statement = $conn->prepare(
                    'INSERT INTO higherorlower_users (user_id, username, email, password, country) VALUES (?, ?, ?, ?, ?)'
                );
                $statement->execute([$user_id, $username, $email, $password, $country]);
                header("Location: login.php");
                die;

            } else {
                echo "Kérjük, adj meg érvényes adatokat!";
            }

        } elseif ($form_mode == "login") {

            if (!empty($email) && !empty($password)) {
                //olvasasás az adatbázisból
                $statement = $conn->prepare('SELECT * FROM higherorlower_users WHERE email = ? LIMIT 1');
                $statement->execute([$email]);
                $user_data = $statement->fetch();
                if ($user_data && $user_data['password'] === $password) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    unset($_SESSION['is_guest']);
                    header("Location: index.php");
                    die;
                }
                echo "Hibás e-mail-cím vagy jelszó.";
            } else {
                echo "Kérjük, add meg az e-mail-címedet és a jelszavadat!";
            }
        }
    }




    ?>

</head>

<body data-bs-theme="dark">
    <a href="../" class="position-absolute top-0 start-0 p-4" style="color: #fff; font-size:20px;"><i class="fa-solid fa-arrow-left"></i></a>

    <div class="container  d-flex justify-content-center">
        <div class="container border m-5 rounded-3 p-4">
            <h1 class="text-center" id="signTxt">Bejelentkezés</h1>
            <form method="post">
                <input type="hidden" name="formtype" id="formtype" value="login">
                <div class="mb-3">
                    <label for="emailInput" class="form-label">E-mail-cím</label>
                    <input type="email" class="form-control" id="emailInput" name="email"
                        placeholder="pelda@email.hu">
                </div>
                <div class="mb-3 signuponly" hidden>
                    <div class="mb-3">
                        <label for="nameInput" class="form-label">Felhasználónév</label>
                        <input type="text" class="form-control" id="nameInput" name="username">
                    </div>
                    <label for="selectCountry" class="form-label">Ország kiválasztása:</label>
                    <div class="dropdown">
                        <div class="selectCountry">
                            <div>
                                <img class="flag"
                                    src="https://purecatamphetamine.github.io/country-flag-icons/3x2/HU.svg">
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
                    <input type="hidden" name="country" id="countryInput" value="HU">
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Jelszó</label>
                    <input type="password" class="form-control" id="passwordInput" name="password">
                </div>
                <div class="text-center">
                    <div class="row">
                        <button class="btn btn-secondary btn-lg" id="loginbtn" type="submit">Bejelentkezés</button>
                    </div>
                    <div class="row mt-2">
                        <button class="btn btn-outline-light btn-lg" type="submit" name="guest" value="1">Játék vendégként</button>
                    </div>
                    <div class="row mt-2">
                        <span id="signupTxt">Még nincs fiókod? <a href="javascript:void(0)" id="signupBtn"
                                style="text-decoration: none; font-weight: bolder;">Regisztráció</a></span>
                        <span id="goBackTxt" hidden><a href="javascript:void(0)"
                                style="text-decoration: none; font-weight: bolder;" id="gotologin">Vissza a bejelentkezéshez</a></span>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>

<!-- https://www.paypal.com/hu/welcome/signup/#/login_info  Tökéletes ország választó | Reszponzív-->