<?php
include 'main.php';
$accounts = $con->query('SELECT * FROM accounts WHERE cast(registered as DATE) = cast(now() as DATE) ORDER BY registered DESC')->fetch_all(MYSQLI_ASSOC);
$accounts_total = $con->query('SELECT COUNT(*) AS total FROM accounts')->fetch_object()->total;
$inactive_accounts = $con->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetch_object()->total;
$active_accounts = $con->query('SELECT * FROM accounts WHERE last_seen > date_sub(now(), interval 1 day) ORDER BY last_seen DESC')->fetch_all(MYSQLI_ASSOC);
$active_accounts2 = $con->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetch_object()->total;
?>
<?=template_admin_header('Dashboard', 'dashboard')?>

<h2>Dashboard</h2>

<div class="dashboard">
    <div class="content-block stat">
        <div>
            <h3>New Accounts (&lt;1 day)</h3>
            <p><?=number_format(count($accounts))?></p>
        </div>
        <i class="fas fa-user-plus"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Total Accounts</h3>
            <p><?=number_format($accounts_total)?></p>
        </div>
        <i class="fas fa-users"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Active Accounts (&lt;30 days)</h3>
            <p><?=number_format($active_accounts2)?></p>
        </div>
        <i class="fas fa-user-clock"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Inactive Accounts (&gt;30 days)</h3>
            <p><?=number_format($inactive_accounts)?></p>
        </div>
        <i class="fas fa-user-clock"></i>
    </div>

</div>

<h2>New Accounts <span>(&lt;1 day)</span></h2>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$accounts): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no newly registered accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?=$account['id']?></td>
                    <td><?=$account['username']?></td>
                    <td class="responsive-hidden"><?=$account['email']?></td>
                    <td class="responsive-hidden"><?=$account['activation_code'] ? $account['activation_code'] : '--'?></td>
                    <td class="responsive-hidden"><?=$account['role']?></td>
                    <td class="responsive-hidden"><?=$account['registered']?></td>
                    <td class="responsive-hidden" title="<?=$account['last_seen']?>"><?=time_elapsed_string($account['last_seen'])?></td>
                    <td>
                        <a href="account.php?id=<?=$account['id']?>">Edit</a>
                        <a href="accounts.php?delete=<?=$account['id']?>" onclick="return confirm('Are you sure you want to delete this account?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<br><br>

<h2>Active Accounts <span>(&lt;1 day)</span></h2>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$active_accounts): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no active accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($active_accounts as $account): ?>
                <tr>
                    <td><?=$account['id']?></td>
                    <td><?=$account['username']?></td>
                    <td class="responsive-hidden"><?=$account['email']?></td>
                    <td class="responsive-hidden"><?=$account['activation_code'] ? $account['activation_code'] : '--'?></td>
                    <td class="responsive-hidden"><?=$account['role']?></td>
                    <td class="responsive-hidden"><?=$account['registered']?></td>
                    <td class="responsive-hidden" title="<?=$account['last_seen']?>"><?=time_elapsed_string($account['last_seen'])?></td>
                    <td>
                        <a href="account.php?id=<?=$account['id']?>">Edit</a>
                        <a href="accounts.php?delete=<?=$account['id']?>" onclick="return confirm('Are you sure you want to delete this account?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>