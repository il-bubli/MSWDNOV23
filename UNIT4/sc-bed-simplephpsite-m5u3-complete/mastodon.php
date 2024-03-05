<?php

$mastodonDns = dns_get_record("mastodon.social", DNS_ALL);

function getDnsRecordValue(array $record): int|string
{
    switch ($record['type']) {
        case 'A':
            return $record['ip'];
        case 'MX':
            return "{$record['pri']} {$record['target']}";
        case 'TXT':
            return $record['txt'];
        case 'AAAA':
            return $record['ipv6'];
        case 'SOA':
            return "{$record['mname']} {$record['rname']}";
        case 'SRV':
            return "{$record['pri']} {$record['weight']} {$record['target']}";
        case 'CNAME':
        case 'NS':
        case 'PTR':
            return $record['target'];
        default:
            return 'Unsupported record type';
    }
}

function getMastodonPosts(string $tag)
{
    $url = "https://mastodon.social/api/v1/timelines/tag/$tag?limit=12";
    $json_data = file_get_contents($url);
    $response_data = json_decode($json_data);
    return $response_data;
}
?>

<!doctype html>
<html lang="en">

<?php include 'includes/head.php' ?>

<body <?= isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'true' ? 'data-bs-theme="dark"' : '' ?>>
    <?php include 'includes/menu.php' ?>

    <div class="container">
        <h1>PHP @ Mastodon</h1>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            
            <?php
            foreach (getMastodonPosts('php') as $post) {
             ?>
            <div class="col">
                <div class="card h-100">
                    <?php if (isset($post->media_attachments[0])) {
                        echo "<img src='{$post->media_attachments[0]->preview_url}' class='card-img-top' alt='{$post->media_attachments[0]->description}'>";
                    }
                     ?>
                    <div class="card-body">
                         <p><?= $post->content ?></p>
                    </div>
                    <div class="card-footer">
                        <?= "<img src='{$post->account->avatar_static}' alt='{$post->account->display_name}' style='max-width: 30px; border-radius: 50%;'>"; ?>
                        <?= $post->account->display_name ?>
                    </div>
                </div>
            </div>
            
            <?php } ?>
        </div>
    <p>
                Data retreived from <a href="https://mastodon.social" target="_blank">mastodon.social</a>
            </p>
        
            <table class="table">
                <thead>
                    <tr>
                        <th>Record Type</th>
                        <th>Value</th>
                        <th>TTL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($mastodonDns as $key => &$value) {
                        echo '<tr>';
                        echo "<td>{$value['type']}</td>";
                        echo '<td>' . getDnsRecordValue($value) . '</td>';
                        echo "<td>{$value['ttl']}</td>";
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        
    </div>

    <?php include 'includes/footer.php' ?>
</body>

</html>