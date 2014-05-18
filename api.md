MATA-PHP API
============

Here are some random notes about the API, not yet structured.

- The whole API lives in the namespace `mataphp`, so for example the function `getSchools` can be accessed like `mataphp\getSchools()`.
- All the exceptions raised in the API are strings starting with `"Mataphp:"`, the function in which the error occured, `":"`, the name of the error without spaces, `", "` and a short description. An example is `"Mataphp:getSchools:invalid_server_response, server returned invalid response"`.

##List of things in the API

###Classes

- `School`: holds the data for a school as returned in an array by `getSchools()` and required by `login()`.
	- **Usage**: You only have to use the `name` property to choose the right school from the array returned by `getSchools()`.
	- **Properties**: `name`, `url`.
	- **Functions**: `__constructor()` mirrors `set()`, `set($name,$url)`.
- `Session`: holds the data for a session. Returned by `login()` and required by functions dealing with a logged in session.
	- **Usage**: Apart from `realName`, which could be useful, one does not need to access properties of objects of this class. You just have to pass it on to API functions.
	- **Properties**: `school`, `userId`, `sessionId`, `realName`.
	- **Functions**: `__constructor()` mirrors `set()`, `set($school,$userId,$sessionId,$realName)`.

- `StudyGuideList`: holds the data for all Study Guides as a list. Returned by `getStudyGuidesList($session)`.
	- **Usage**: It is not recommended to access this class directly. You just have to pass it a session to the `getStudyGuidesList()` function.
	- **Properties**: `title`,`id`,`startDate`,`endDate`,`subject`.
	- **Functions**: `__constructor()` mirrors `set()`, `set($title,$id,$startDate,$endDate,$subject)`.

- `StudyGuide`: holds the general information for a Study Guide. Returned by `getStudyGuideContent($session,$StudyGuideId)`.
	- **Usage**: You just have to pass it a correct Study Guide Id and a session to the `getStudyGuideContent()` function.
	- **Properties**: `id`,`startDate`,`endDate`,`title`,`subject`,`archived`,`content`.
	- **Functions**: `__constructor()` mirrors `set()`, `set($id,$startDate,$endDate,$title,$subject,$archived,$content )`.

- `StudyGuideContent`: holds the data for all the content items of a Study Guide. Required by `getStudyGuideContent()`.
	- **Usage**: It is not recommended to access this class directly. You just have to pass it a correct Study Guide Id and a session to the `getStudyGuideContent` function and it will store all the content items and their Attachments.
	- **Properties**: `title`,`content`,`attachments`
	- **Functions**: `__constructor()` mirrors `set()`, `set($title,$content,$attachments)`.

- `StudyGuideAttachments`: holds the data for all the attachments of a Study Guide Content Item. Required by `getStudyGuideContent()` `StudyGuideContent` class.
	- **Usage**: It is not recommended to access this class directly. You just have to pass it a correct Study Guide Id and a session to the `getStudyGuideContent` function and it will store the Attachments. automatically.
	- **Properties**: `title`,`type`,`url`
	- **Functions**: `__constructor()` mirrors `set()`, `set($title,$type,$url)`.

###Functions

- `getSchools($filter)`: polls the server for schools matching the filter.
	- **Returns**: array of `School` items.
	- **Parameters**:
		- `filter`: the search string to search for.
- `login($school,$username,$password)`: tries to log in with the specified credentials.
	- **Returns**: a `Session` item if the login was successful, otherwise `false`.
	- **Parameters**:
		- `school`: which school to log in to, of type `School`
		- `username`: username for login
		- `password`: password for login
- `getStudyGuideList($session)`: get all Study Guides as a list.
	- **Returns**: array of `StudyGuidesList` items:
		- `title`: the title of the Study Guide.
		- `id`: the id of the Study Guide.
		- `startDate`: the start date of the Study Guide.
		- `endDate`: the end date of the Study Guide.
		- `subject`: the subject of the Study Guide. NOTE: We don't have currently a list with all possible subjects.
	- **Parameters**:
		- `$session`: just the given session you've got from the `login($school,$username,$password)` function.
- `getStudyGuideContent($session,$studyguideId)`: get all the properties, content, all hyperlinks and all attachments of a Study Guide item.
	- **Returns**: array of the general `StudyGuide` properties:
		- `id`: The id of the Study Guide.
		- `startDate`: the start date of the Study Guide.
		- `endDate`: the end date of the Study Guide.
		- `title`: the title of a Study Guide Content. 
		- `archived`: if the Study Guide is archived it returns 1 otherwise 0.
		- `subject`: the subject of the studyguide. NOTE: We don't have currently a list with all possible subjects.
		- `content`: an array of all the Study Guide's Content items.
				- `title`: the title of a Study Guide Content item. 
				- `content`: the content of a Study Guide Content item. May contains fixed hyperlinks in plain HTML format.
				- `attachments`: array of `StudyGuideAttachments` items:
						- `title`: the title of the attachment.
						- `type`: the type of the attachment. 1 = Just a normal attachment, 2 = An assignment, 3 = A website, 4 = A Youtube Video.
						- `url`: the URL of the attachment.
	- **Parameters**:
		- `$session`: just the given session you've got from the `login()` function.
		- `$studyguideId`: the Id of a Study Guide, you could use one from the `getStudyGuidesList($session)` function.
