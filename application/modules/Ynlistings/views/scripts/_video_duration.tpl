<span class="video_length">
    <?php
    if ($this->video->duration >= 3600) {
        $duration = gmdate("H:i:s", $this->video->duration);
    } else {
        $duration = gmdate("i:s", $this->video->duration);
    }
    echo $duration;
    ?>
</span>