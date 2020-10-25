<?php
function OpenConnection()
{
    try
    {
        $serverName = "tcp:myserver.database.windows.net,1433";
        $connectionOptions = array("Database"=>"AdventureWorks",
            "Uid"=>"MyUser", "PWD"=>"MyPassword");
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if($conn == false)
            die(FormatErrors(sqlsrv_errors()));
    }
    catch(Exception $e)
    {
        echo("Error!");
    }
}

function ReadData()
{
    try
    {
        $conn = OpenConnection();
        $tsql = "SELECT [CompanyName] FROM SalesLT.Customer";
        $getProducts = sqlsrv_query($conn, $tsql);
        if ($getProducts == FALSE)
            die(FormatErrors(sqlsrv_errors()));
        $productCount = 0;
        while($row = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC))
        {
            echo($row['CompanyName']);
            echo("<br/>");
            $productCount++;
        }
        sqlsrv_free_stmt($getProducts);
        sqlsrv_close($conn);
    }
    catch(Exception $e)
    {
        echo("Error!");
    }
}
    function InsertData()
    {
        try
        {
            $conn = OpenConnection();

            $tsql = "INSERT SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate) OUTPUT            INSERTED.ProductID VALUES ('SQL Server 1', 'SQL Server 2', 0, 0, getdate())";
            //Insert query
            $insertReview = sqlsrv_query($conn, $tsql);
            if($insertReview == FALSE)
                die(FormatErrors( sqlsrv_errors()));
            echo "Product Key inserted is :";
            while($row = sqlsrv_fetch_array($insertReview, SQLSRV_FETCH_ASSOC))
            {
                echo($row['ProductID']);
            }
            sqlsrv_free_stmt($insertReview);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }
?>


