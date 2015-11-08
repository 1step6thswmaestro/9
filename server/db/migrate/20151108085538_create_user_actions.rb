class CreateUserActions < ActiveRecord::Migration
  def change
    create_table :user_actions do |t|
      t.string :tracking_id
      t.string :name
      t.string :value

      t.timestamps null: false
    end
  end
end
