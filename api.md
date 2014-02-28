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
  - **Properties**: `school`, `userId`, `realName`.
  - **Functions**: `__constructor()` mirrors `set()`, `set($school,$userId,$realName)`.
