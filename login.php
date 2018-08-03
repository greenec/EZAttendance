<?php

$title = 'Easton NHS Login';

require 'include/header.php';

?>

	<!-- Form -->
    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Login
                        </div>
                        <div class="card-body">
                            <form id="login-form">
                                <div class="form-group row" id="role-group">
                                    <div class="col-sm-2 control-label text-sm-right">
                                        <label for="role">Role</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="role" id="role">
                                            <option value="">Please select your role</option>
                                            <option value="officer">Club Officer</option>
                                            <option value="teacher">Teacher</option>
                                            <option value="admin">System Administrator</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="email-group">
                                    <div class="col-sm-2 control-label text-sm-right">
                                        <label for="email">Email</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="email-extension">@roverkids.org</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" id="password-group">
                                    <div class="col-sm-2 control-label text-sm-right">
                                        <label for="password">Password</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-9">
                                        <button type="submit" class="btn btn-primary login">Login</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.container -->
                </div>
            </div>
        </div>
    </main>

	<!-- required JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- custom scripts -->
	<script src="js/login.js"></script>
</body>
</html>
