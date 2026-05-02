<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up - Village Bank</title>
    <link rel="stylesheet" href="../../static/css/signup.css">
</head>

<body>

    <div class="layout">
        <!-- container -->
        <main class="main">

            <h1>Join Our Group</h1>

            <form class="form">

                <section class="card">
                    <h3>Personal Details</h3>

                    <div class="grid">
                        <div>
                            <label>First Name</label>
                            <input type="text" placeholder="Enter first name">
                        </div>

                        <div>
                            <label>Last Name</label>
                            <input type="text" placeholder="Enter last name">
                        </div>

                        <div>
                            <label>Gender</label>
                            <div class="radio-group">
                                <label><input type="radio" name="gender"> Male</label>
                                <label><input type="radio" name="gender"> Female</label>
                            </div>
                        </div>

                        <div>
                            <label>National ID</label>
                            <input type="text" placeholder="ID number">
                        </div>
                    </div>
                </section>

                <section class="card">
                    <h3>Contact Details</h3>

                    <div class="grid">
                        <div>
                            <label>Phone Number</label>
                            <input type="text" placeholder="e.g. 099...">
                        </div>

                        <div>
                            <label>Address</label>
                            <textarea placeholder="Physical address"></textarea>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <h3>Emergency Contact</h3>

                    <div class="grid">
                        <div>
                            <label>Full Name</label>
                            <input type="text" placeholder="Next of kin name">
                        </div>

                        <div>
                            <label>Relationship</label>
                            <select>
                                <option>Sibling</option>
                                <option>Parent</option>
                                <option>Spouse</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div>
                            <label>Phone Number</label>
                            <input type="text" placeholder="Next of kin phone">
                        </div>
                    </div>
                </section>

                <div class="buttons">
                    <button type="submit" class="btn primary">Save Member</button>
                    <button type="reset" class="btn danger">Reset</button>
                    <a class="btn primary" style="text-decoration: none;" href="/auth/login.php">Back to login</a>
                </div>

            </form>

        </main>

    </div>

</body>

</html>