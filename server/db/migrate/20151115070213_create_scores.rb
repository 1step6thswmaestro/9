class CreateScores < ActiveRecord::Migration
  def change
    create_table :scores do |t|
      t.string :action_name
      t.integer :reaction_id
      t.integer :score

      t.timestamps null: false
    end
  end
end
