<div id="dynamic_content" class="main_screen">
<?php
require($PAGESDIR . '/base/leftnav.inc');
?>
<div id="main_content">
<?php
/* Retrieve and sanitize POST data */
$arguments = array(
    'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
    'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'email'     => FILTER_SANITIZE_EMAIL,
);
$searchCriteria = filter_input_array( INPUT_POST, $arguments );

    /**
     * Search Query
     *************************************************/

    /* Formulate the search query */
    $search = array( 
        'method' => 'account/listAccounts', 
        'columns' => array(
            'standardFields' => array('First Name', 'Last Name', 'Email 1', 'City', 'State' ),
        ),
        'page' => array(
            'currentPage' => 1,
            'pageSize' => 200,
            'sortColumn' => 'Last Name',
            'sortDirection' => 'ASC',
        ),
    );

    /* Some search criteria are variable based on our POST data. Add them to the query if applicable */
    if ( isset( $searchCriteria['firstName'] ) && !empty( $searchCriteria['firstName'] ) ) {
        $search['criteria'][] = array( 'First Name', 'EQUAL', $searchCriteria['firstName'] );
    }
    if ( isset( $searchCriteria['lastName'] ) && !empty( $searchCriteria['lastName'] ) ) {
        $search['criteria'][] = array( 'Last Name', 'EQUAL', $searchCriteria['lastName'] );
    }
    if ( isset( $searchCriteria['email'] ) && !empty( $searchCriteria['email'] ) ) {
        $search['criteria'][] = array( 'Email', 'EQUAL', $searchCriteria['email'] );
    }

    /**
     * Execute search
     **************************************************/ 

    /* If there are search criteria present, execute the search query */
    if ( !empty( $search['criteria'] ) ) {
        $result = $GLOBALS['Neon']->search($search);
        $message = 'No results match your search.';
    } else {
        $result = null;
        $message = 'You must specify search criteria.';
    }

?>
        <div class="row">
            <div class="col-sm-12">
                <h1>Account Search</h1>
                <form action="index.php?Function=lookup" method="POST" class="form-inline">
                    <fieldset>
                        <legend>Search Criteria</legend>
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="firstName" value="<?php echo htmlentities( $searchCriteria['firstName'] ); ?>"/>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lastName" value="<?php echo htmlentities( $searchCriteria['lastName'] ); ?>" />
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" name="email" value="<?php echo htmlentities( $searchCriteria['email'] ); ?>" />
                            </div>
                            <input type="submit" value="Search" class="btn btn-default" />
                    </fieldset>
                </form>
            </div><!--.col-sm-12-->
        </div><!--.row-->
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <?php
                /**
                 * Iterate through API results
                 *******************************************/
                ?>
                <?php if( isset($result['page']['totalResults'] ) && $result['page']['totalResults'] >= 1 ): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result['searchResults'] as $r): ?>
                        <tr>
                            <td><?php echo $r['First Name']; ?> <?php echo $r['Last Name']; ?></td>
                            <td><?php echo $r['Email 1']; ?></td>
                            <td><?php echo $r['City']; ?> <?php echo $r['State']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p><?php echo $message; ?></p>
                <?php endif; ?>
            </div><!--.col-sm-12-->
        </div><!--.row-->

</div><!--close main_content div-->
