CREATE TABLE schedule (
  guid           VARCHAR(32)                                                                                                                                                                           NOT NULL PRIMARY KEY,
  time           INT UNSIGNED DEFAULT 0,
  days           SET ('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31') NOT NULL,
  week_days      SET ('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')                                                                                                    NOT NULL,
  months         SET ('janary', 'fabruary', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december')                                                         NOT NULL,
  subject        VARCHAR(64)                                                                                                                                                                           NOT NULL,
  identify       VARCHAR(64)                                                                                                                                                                           NOT NULL,
  action         VARCHAR(64)                                                                                                                                                                           NOT NULL,
  scope          TEXT,
  scope_hash     VARCHAR(32)                                                                                                                                                                           NOT NULL,
  last_execution INT UNSIGNED DEFAULT 0,
  UNIQUE KEY (subject, identify, action, scope_hash, time, days, week_days, months),
  INDEX `schedule_time` (last_execution, time, months, week_days, days)
);