<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.rawgit.com/angular/bower-material/v1.0.6/angular-material.min.css">
    <link rel="stylesheet" href="https://cdn.rawgit.com/alenaksu/mdPickers/0.7.4/dist/mdPickers.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="dist/bootstrap-clockpicker.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Add Rule</title>


</head>


<body>
    <div class="container">
        <nav class="navbar navbar-light bg-info">
            <div class="container-fluid">
                <a class="navbar-brand logo" href="#">Parkbud.ca</a>
                <span>logged in as : {User}</span>


            </div>
        </nav>


        <div class="container-fluid">
            <h3 class="logo ">Add Rule</h1>
                <div class="row">


                    <div class="col-4 map">
                        <input class="" type="text">
                        <!-- Google Map Place Holder Code -->
                        <iframe class="row " src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3506.2233913121413!2d77.4051603706222!3d28.50292593193056!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce626851f7009%3A0x621185133cfd1ad1!2sGeeksforGeeks!5e0!3m2!1sen!2sin!4v1585040658255!5m2!1sen!2sin" width="300" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0">
                        </iframe>


                    </div>
                    <div class="col-4">

                        <form>
                            <!-- Display Street Name from Map Search -->
                            <div class="form-group row text-center">
                                <label for="exampleInputEmail1">Street Name</label>
                                <input type="email" class="form-control" id="streetname" aria-describedby="streetname" placeholder="street name">

                            </div>
                            <!-- Select Time Period For Applicable parking rules -->
                            <div class="form-group row text-center">
                                <label for="exampleInputEmail1">Period start</label>
                                <input type="date" class="form-control" id="dateStart">

                            </div>
                            <div class="form-group row text-center">
                                <label for="exampleInputEmail1">Period End</label>
                                <input type="date" class="form-control" id="dateEnd">

                            </div>

                            <!-- Set Parking restriction times per Day -->
                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" name="monday" class="form-check-input col" id="monCheck">
                                    <label class="form-check-label col-3" for="exampleCheck1">Monday</label>

                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>

                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Tuesday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>

                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Wenesday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>

                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Thursday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>

                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Friday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>
                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Saturday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>
                            <div class="form-check container-fluid daySlot">
                                <div class="">

                                    <input type="checkbox" class="form-check-input col" id="exampleCheck1">
                                    <label class="form-check-label col-3" for="exampleCheck1">Sunday</label>
                                    <div class="col">
                                        start<input class="timeInput" type="time" ng-model="test" mdp-time-picker /><br>
                                        end<input class="timeInput" type="time" ng-model="test" mdp-time-picker />
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>


                    <div class="col-4 text-center">
                        <h1>no parking</h1>
                    </div>
                </div>

        </div>
    </div>






    <script>

    </script>
    <script src="https: //ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>

</html>