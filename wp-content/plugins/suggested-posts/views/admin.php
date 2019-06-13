<style>
.supo-container {
    margin:auto;
    width: 75%;
}
.supo-text-area {
    width:100%;
}
label {
    vertical-align: middle;
}
</style>
<div class="supo-container">
    <form method="post">
        <h1>Suggested Posts Settings</h1>
        <label for="supo_tags">Tags (seperate multiple tags with commas</label>
        <textarea name="supo_tags" id="supo_tags" rows="3" class="supo-text-area regular-text"><?=$tags?></textarea>
        <input type="submit" name="submit" id="submit" value="Save" class="button button-primary">
    </form>
</div>