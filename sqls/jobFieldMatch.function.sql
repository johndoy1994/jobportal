DELIMITER ;;
DROP FUNCTION IF EXISTS jobFieldMatch ;;
CREATE FUNCTION jobFieldMatch(_field varchar(255), _job int, _user int, _data text) RETURNS int
BEGIN

    DECLARE user_id int;
    DECLARE job_id int;
    DECLARE intTmp int;
    DECLARE intTmp1 int;
    DECLARE intTmp2 int;
    DECLARE intTmp3 int;
    DECLARE varcharTmp varchar(255);

    SELECT id INTO user_id FROM users WHERE id = _user;
    SELECT id INTO job_id FROM jobs WHERE id = _job;

    if user_id = _user AND job_id = _job then
    	case

    		when _field = "tag" then

	   			SELECT tag_id INTO intTmp FROM user_skills WHERE user_id = user_id AND tag_id = cast(_data as signed) limit 1;

    			if intTmp = cast(_data as signed) then
    				return 1;
    			end if;

    		when _field = "certificate" then

    			SELECT certificate INTO varcharTmp FROM user_certificates WHERE user_id = user_id AND certificate = _data limit 1;

    			if varcharTmp = _data then
    				return 1;
    			end if;

    		when _field = "experience_level_id" then

    			SELECT experinece_level_id INTO intTmp FROM user_experiences WHERE user_id = user_id limit 1;
    			SELECT experience_level_id INTO intTmp1 FROM jobs WHERE id = job_id limit 1;

    			if intTmp = intTmp1 then
    				return 1;
    			end if;

    		when _field = "experience_id" then

    			SELECT experiences.order INTO intTmp FROM user_experiences, experiences WHERE user_experiences.user_id = user_id and experiences.id = user_experiences.experinece_id limit 1;
    			SELECT experiences.order INTO intTmp1 FROM jobs, experiences WHERE jobs.id = job_id and experiences.id = jobs.experience_id limit 1;

    			if intTmp1 <= intTmp then
    				return 1;
    			end if;

    		when _field = "education_id" then

    			SELECT education.order INTO intTmp FROM user_experiences, education WHERE user_experiences.user_id = user_id and education.id = user_experiences.education_id limit 1;
    			SELECT education.order INTO intTmp1 FROM jobs, education WHERE jobs.id = job_id and education.id = jobs.education_id limit 1;

    			if intTmp1 <= intTmp then
    				return 1;
    			end if;

    			// Salary
    			// CityId
    			// JobTitle id
    			// JobType Id

    			// and after this create getMatchingCount();

    	end case;
    end if;

    return 0;

END ;;

SELECT jobFieldMatch("education_id", 1, 5, "");