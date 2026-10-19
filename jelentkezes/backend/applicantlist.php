<?php
include_once("applicant.php");
include_once("db/conn.php");
class Applicantlist
{
    public $conn;
    public function __construct()
    {
        $this->conn = $GLOBALS["conn"];
    }
    public function getAll()
    {
        $sql = "SELECT * FROM jelentkezok ORDER BY Id DESC;";
        $result = $this->conn->query($sql);
        $applicants = array();
        foreach ($result as $row) {
            $applicants[] = new Applicant((int)$row["Id"], $row["Full_name"], $row["CurrentSchool"], explode(",", $row["Major_Name"]), (int)$row["Dorm"], (int)$row["isSenior"]);
        }
        return $applicants;
    }
    public function getStats()
    {
        $sql = "SELECT COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN isSenior = 1 THEN 1 ELSE 0 END), 0) AS senior,
            COALESCE(SUM(CASE WHEN Dorm = 1 THEN 1 ELSE 0 END), 0) AS dorm,
            COALESCE(SUM(CASE WHEN Dorm = 0 THEN 1 ELSE 0 END), 0) AS commuter,
            COALESCE(SUM(CASE WHEN Dorm = 2 THEN 1 ELSE 0 END), 0) AS unsure,
            COALESCE(SUM(CASE WHEN Dorm = 1 AND isSenior = 1 THEN 1 ELSE 0 END), 0) AS dorm_senior
            FROM jelentkezok;";
        $row = $this->conn->query($sql)->fetch();
        return array(
            "total" => (int)$row["total"],
            "senior" => (int)$row["senior"],
            "dorm" => (int)$row["dorm"],
            "commuter" => (int)$row["commuter"],
            "unsure" => (int)$row["unsure"],
            "dorm_senior" => (int)$row["dorm_senior"]
        );
    }
    public function getMajorStats($majorNames)
    {
        $majors = array();
        $statement = $this->conn->prepare("SELECT COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN isSenior = 1 THEN 1 ELSE 0 END), 0) AS senior,
            COALESCE(SUM(CASE WHEN Dorm = 1 THEN 1 ELSE 0 END), 0) AS dorm,
            COALESCE(SUM(CASE WHEN Dorm = 1 AND isSenior = 1 THEN 1 ELSE 0 END), 0) AS dorm_senior
            FROM jelentkezok
            WHERE instr(',' || Major_Name || ',', ',' || :major || ',') > 0;");
        foreach ($majorNames as $major) {
            $statement->execute(array("major" => $major));
            $row = $statement->fetch();
            $majors[] = array(
                "major" => $major,
                "total" => (int)$row["total"],
                "senior" => (int)$row["senior"],
                "dorm" => (int)$row["dorm"],
                "dorm_senior" => (int)$row["dorm_senior"]
            );
        }
        usort($majors, function ($a, $b) {
            return $b["total"] - $a["total"];
        });
        return $majors;
    }
    public function delete($id)
    {
        $statement = $this->conn->prepare("DELETE FROM jelentkezok WHERE Id = :id;");
        return $statement->execute(array("id" => (int)$id));
    }
    public function create($name, $school, $majors, $dorm, $senior)
    {
        $statement = $this->conn->prepare("INSERT INTO jelentkezok (Full_name, CurrentSchool, Major_Name, Dorm, isSenior)
            VALUES (:name, :school, :majors, :dorm, :senior);");
        return $statement->execute(array(
            "name" => $name,
            "school" => $school,
            "majors" => implode(",", $majors),
            "dorm" => (int)$dorm,
            "senior" => (int)$senior
        ));
    }
}
