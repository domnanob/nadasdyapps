CREATE TABLE jelentkezok (
  Id            INTEGER PRIMARY KEY AUTOINCREMENT,
  Full_name     TEXT,
  CurrentSchool TEXT,
  Major_Name    TEXT,
  Dorm          INTEGER DEFAULT 0,
  isSenior      INTEGER DEFAULT 0
);