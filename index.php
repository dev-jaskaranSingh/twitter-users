<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <link media="all" rel="stylesheet" href="./public/css/all.css">
    </head>
    <body>
        <div id="wrapper">
            <div class="container">
                <form class="form" id="searchUsers">
                    <fieldset>
                        <div class="row">
                            <div class="col">
                                <input name="location" value="Pakistan" id="locationTextField" type="search">
                            </div>
                            <div class="col">
                                <input name="from" value="50k" type="text" placeholder="From">
                            </div>
                            <div class="col">
                                <input name="to" value="100k" type="text" placeholder="To">
                            </div>
                            <div class="col">
                                <button type="submit">Search</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th>Twitter users</th>
                                <th>Followers</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">No data found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
        <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>
        <script src="./public/js/script.js"></script>
    </body>
</html>