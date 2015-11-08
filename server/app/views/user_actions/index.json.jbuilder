json.array!(@user_actions) do |user_action|
  json.extract! user_action, :id, :tracking_id, :name, :value
  json.url user_action_url(user_action, format: :json)
end
